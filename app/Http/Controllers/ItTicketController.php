<?php

namespace App\Http\Controllers;

use App\Models\ItTicket;
use App\Models\User;
use App\Services\ItTicketPushService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ItTicketController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $canManage = $this->canManage($user);
        $canViewOnly = $user->isHeadOfStore2();

        if ($canManage) {
            $tickets = ItTicket::with(['requester.employee.divisions', 'assignee.employee'])->latest()->get();
        } elseif ($canViewOnly) {
            $tickets = ItTicket::with(['requester.employee.divisions', 'assignee.employee'])->latest()->get();
        } else {
            $tickets = ItTicket::with('assignee.employee')->where('requester_id', $user->id)->latest()->get();
        }

        $itUsers = $canManage
            ? User::with('employee')->whereIn('role', [User::ROLE_KOORDINATOR_IT, User::ROLE_STAFF_IT])
                ->whereHas('employee', fn ($q) => $q->whereIn('nama', ['Ahmad Musyadad Haury', 'Muhammad Ilyas Al-Fadhlih', 'Muhamad Fijan Natinnaim Fauzi']))
                ->orderBy('role')->orderBy('employee_id')->get()
            : collect();

        $canDelete = $user->isKoordinatorIt();
        $canGiveFeedback = $canViewOnly;

        $stats = [
            'total' => $tickets->count(),
            'diproses' => $tickets->where('status', 'diproses')->count(),
            'dijeda' => $tickets->where('status', 'dijeda')->count(),
            'selesai' => $tickets->where('status', 'selesai')->count(),
        ];

        return view('it.tickets', compact('tickets', 'itUsers', 'canManage', 'canDelete', 'canViewOnly', 'canGiveFeedback', 'stats'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'submission_key' => ['required', 'uuid'],
            'judul' => ['required', 'string', 'max:150'],
            'deskripsi' => ['required', 'string', 'max:3000'],
            'kategori' => ['required', 'in:perangkat,aplikasi,akun_akses,jaringan,lainnya'],
            'prioritas' => ['required', 'in:rendah,sedang,tinggi,mendesak'],
            'bukti_kendala' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $submissionKey = $data['submission_key'];
        unset($data['submission_key']);
        $requester = $request->user();

        $existingTicket = ItTicket::where('submission_key', $submissionKey)
            ->where('requester_id', $requester->id)
            ->first();

        if ($existingTicket) {
            return $this->ticketSubmissionResponse($request, $existingTicket, true);
        }

        $storedEvidence = null;
        if ($request->hasFile('bukti_kendala')) {
            $storedEvidence = $request->file('bukti_kendala')->store('bukti-kendala', 'public');
            $data['bukti_kendala'] = $storedEvidence;
        }

        try {
            $ticket = DB::transaction(function () use ($data, $requester, $submissionKey) {
                $todayPrefix = 'IT-' . now()->format('Ymd') . '-';
                $lastKode = ItTicket::where('kode', 'like', $todayPrefix . '%')->lockForUpdate()->max('kode');
                $nextNumber = $lastKode ? ((int) substr($lastKode, -3)) + 1 : 1;

                return ItTicket::create([
                    ...$data,
                    'requester_id' => $requester->id,
                    'submission_key' => $submissionKey,
                    'kode' => $todayPrefix . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT),
                ]);
            });
        } catch (QueryException $exception) {
            $existingTicket = ItTicket::where('submission_key', $submissionKey)
                ->where('requester_id', $requester->id)
                ->first();

            if ($existingTicket) {
                if ($storedEvidence && $existingTicket->bukti_kendala !== $storedEvidence) {
                    Storage::disk('public')->delete($storedEvidence);
                }

                return $this->ticketSubmissionResponse($request, $existingTicket, true);
            }

            if ($storedEvidence) {
                Storage::disk('public')->delete($storedEvidence);
            }

            throw $exception;
        }

        $itUsers = User::whereIn('role', [User::ROLE_KOORDINATOR_IT, User::ROLE_STAFF_IT])->get();
        app(ItTicketPushService::class)->sendToUsers(
            $itUsers->reject(fn (User $itUser) => $itUser->is($requester)),
            'Tiket IT baru',
            $ticket->kode . ' · ' . $ticket->judul,
            $ticket,
        );

        return $this->ticketSubmissionResponse($request, $ticket, false);
    }

    private function ticketSubmissionResponse(Request $request, ItTicket $ticket, bool $duplicate): RedirectResponse|JsonResponse
    {
        $message = $duplicate
            ? "Pengajuan {$ticket->kode} sudah diterima sebelumnya. Tiket tidak dibuat ulang."
            : "Tiket {$ticket->kode} berhasil dikirim.";

        if ($request->expectsJson()) {
            $request->session()->flash('success', $message);

            return response()->json([
                'message' => $message,
                'ticket_code' => $ticket->kode,
                'duplicate' => $duplicate,
            ]);
        }

        return back()->with('success', $message);
    }

    public function evidence(Request $request, ItTicket $ticket)
    {
        $user = $request->user();
        abort_unless(
            $ticket->requester_id === $user->id || $this->canManage($user) || $user->isHeadOfStore2(),
            403
        );
        abort_unless($ticket->bukti_kendala, 404);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($ticket->bukti_kendala), 404);

        return $disk->response($ticket->bukti_kendala);
    }

    public function update(Request $request, ItTicket $ticket): RedirectResponse
    {
        $user = $request->user();
        abort_unless($this->canManage($user), 403);

        if ($ticket->status === 'selesai') {
            abort(403, 'Tiket yang sudah selesai tidak dapat diubah.');
        }

        $data = $request->validate([
            'assignee_id' => ['nullable', 'exists:users,id'],
            'status' => ['nullable', 'in:menunggu,diproses,dijeda,dilanjutkan,selesai,ditolak'],
            'catatan_it' => ['nullable', 'string', 'max:3000'],
            'alasan_jeda' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($data['assignee_id'] ?? null) {
            abort_unless(User::whereKey($data['assignee_id'])->whereIn('role', [User::ROLE_KOORDINATOR_IT, User::ROLE_STAFF_IT])->exists(), 422);
        }

        if ($user->isStaffIt() && ($data['assignee_id'] ?? null) && (int) $data['assignee_id'] !== (int) $user->id) {
            abort(403, 'Staff IT hanya dapat menugaskan tiket kepada dirinya sendiri.');
        }

        $data['status'] = $data['status'] ?? $ticket->status;
        if (!array_key_exists('catatan_it', $data)) {
            $data['catatan_it'] = $ticket->catatan_it;
        }

        if ($data['status'] === 'menunggu' && $ticket->status !== 'menunggu') {
            abort(403, 'Tiket yang sudah diproses tidak dapat dikembalikan ke status Menunggu.');
        }

        if ($data['status'] === 'dijeda') {
            $data['alasan_jeda'] = trim((string) ($data['alasan_jeda'] ?? ''));
            if ($data['alasan_jeda'] === '') {
                throw ValidationException::withMessages(['alasan_jeda' => 'Alasan jeda wajib diisi saat status Dijeda.']);
            }
        } else {
            $data['alasan_jeda'] = null;
        }

        if ($ticket->assignee_id && $ticket->assignee_id !== $user->id) {
            if ($data['status'] !== $ticket->status || $data['catatan_it'] !== $ticket->catatan_it || $data['alasan_jeda'] !== $ticket->alasan_jeda) {
                abort(403, 'Hanya PIC yang ditugaskan yang dapat mengubah status dan catatan tiket.');
            }
        }

        $now = now();

        if (in_array($data['status'], ['diproses', 'dilanjutkan'])) {
            if (!$ticket->mulai_ditangani_at) {
                $data['mulai_ditangani_at'] = $now;
            }
            if (!$ticket->proses_mulai_at) {
                $data['proses_mulai_at'] = $now;
            }
        } else {
            if ($ticket->proses_mulai_at) {
                $data['durasi_detik'] = $ticket->durasi_detik + max(0, $ticket->proses_mulai_at->diffInSeconds($now));
                $data['proses_mulai_at'] = null;
            }
        }

        if ($data['status'] === 'selesai' && !$ticket->selesai_at) {
            $data['selesai_at'] = $now;
        }

        $oldAssigneeId = $ticket->assignee_id;
        $oldStatus = $ticket->status;

        $ticket->update($data);

        $recipients = User::whereIn('id', array_filter([
            $ticket->requester_id,
            $oldAssigneeId,
            $ticket->assignee_id,
        ]))->get()->reject(fn (User $recipient) => $recipient->is($user));
        $pushStatus = $ticket->status !== $oldStatus ? 'Status ' . ucfirst($ticket->status) : 'Tiket diperbarui';
        app(ItTicketPushService::class)->sendToUsers(
            $recipients,
            $pushStatus,
            $ticket->kode . ' · ' . $ticket->judul,
            $ticket,
        );

        $isItRole = $user->isKoordinatorIt() || $user->isStaffIt();
        $accepting = $isItRole
            && ($data['assignee_id'] ?? null)
            && (int) $data['assignee_id'] === (int) $user->id
            && (int) $oldAssigneeId !== (int) $user->id;

        if ($accepting) {
            $message = in_array($data['status'], ['diproses', 'dilanjutkan'])
                ? 'Tiket ' . $ticket->kode . ' berhasil diterima dan sedang ditangani.'
                : 'Tiket ' . $ticket->kode . ' berhasil diterima.';
        } else {
            $message = match ($data['status']) {
                'selesai' => 'Tiket ' . $ticket->kode . ' berhasil diselesaikan.',
                'dijeda' => 'Tiket ' . $ticket->kode . ' dihentikan sementara.',
                'dilanjutkan' => 'Tiket ' . $ticket->kode . ' dilanjutkan kembali.',
                'ditolak' => 'Tiket ' . $ticket->kode . ' ditolak.',
                default => 'Tiket ' . $ticket->kode . ' berhasil diperbarui.',
            };
        }

        return back()->with('success', $message);
    }

    public function destroy(Request $request, ItTicket $ticket): RedirectResponse
    {
        abort_unless($request->user()->isKoordinatorIt(), 403);

        if ($ticket->bukti_kendala) {
            Storage::disk('public')->delete($ticket->bukti_kendala);
        }

        app(ItTicketPushService::class)->sendToUsers(
            [$ticket->requester],
            'Tiket dihapus',
            $ticket->kode . ' · ' . $ticket->judul,
            $ticket,
        );

        $ticket->delete();
        return back()->with('success', 'Tiket ' . $ticket->kode . ' dihapus.');
    }

    public function feedback(Request $request, ItTicket $ticket): RedirectResponse
    {
        abort_unless($request->user()->isHeadOfStore2(), 403);

        $request->validate([
            'feedback_atasan' => 'required|string|max:3000',
        ]);

        $ticket->update(['feedback_atasan' => $request->input('feedback_atasan')]);

        $recipients = $ticket->assignee ? collect([$ticket->assignee]) : User::whereIn('role', [User::ROLE_KOORDINATOR_IT, User::ROLE_STAFF_IT])->get();
        app(ItTicketPushService::class)->sendToUsers(
            $recipients,
            'Feedback tiket baru',
            $ticket->kode . ' · ' . $ticket->judul,
            $ticket,
        );

        return back()->with('success', 'Feedback untuk tiket ' . $ticket->kode . ' berhasil disimpan.');
    }

    private function canManage(User $user): bool
    {
        return $user->isKoordinatorIt() || $user->isStaffIt();
    }
}
