<?php

namespace App\Http\Controllers;

use App\Models\ItTicket;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItTicketController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $canManage = $this->canManage($user);

        $tickets = $canManage
            ? ItTicket::with(['requester.employee.division', 'assignee.employee'])->latest()->get()
            : ItTicket::with('assignee.employee')->where('requester_id', $user->id)->latest()->get();

        $itUsers = $canManage
            ? User::with('employee')->whereIn('role', [User::ROLE_KOORDINATOR_IT, User::ROLE_STAFF_IT])
                ->whereHas('employee', fn ($q) => $q->whereIn('nama', ['Ahmad Musyadad Haury', 'Muhammad Ilyas Al-Fadhlih', 'Muhamad Fijan Natinnaim Fauzi']))
                ->orderBy('role')->orderBy('employee_id')->get()
            : collect();

        $canDelete = $user->isKoordinatorIt();

        $stats = [
            'total' => $tickets->count(),
            'diproses' => $tickets->where('status', 'diproses')->count(),
            'dijeda' => $tickets->where('status', 'dijeda')->count(),
            'selesai' => $tickets->where('status', 'selesai')->count(),
        ];

        return view('it.tickets', compact('tickets', 'itUsers', 'canManage', 'canDelete', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'deskripsi' => ['required', 'string', 'max:3000'],
            'kategori' => ['required', 'in:perangkat,aplikasi,akun_akses,jaringan,lainnya'],
            'prioritas' => ['required', 'in:rendah,sedang,tinggi,mendesak'],
        ]);

        $data['requester_id'] = $request->user()->id;
        $data['kode'] = 'IT-' . now()->format('Ymd') . '-' . str_pad((string) (ItTicket::whereDate('created_at', today())->count() + 1), 3, '0', STR_PAD_LEFT);
        ItTicket::create($data);

        return back()->with('success', 'Tiket IT berhasil dikirim.');
    }

    public function update(Request $request, ItTicket $ticket): RedirectResponse
    {
        abort_unless($this->canManage($request->user()), 403);

        $data = $request->validate([
            'assignee_id' => ['nullable', 'exists:users,id'],
            'status' => ['required', 'in:menunggu,diproses,dijeda,dilanjutkan,selesai,ditolak'],
            'catatan_it' => ['nullable', 'string', 'max:3000'],
        ]);

        if ($data['assignee_id'] ?? null) {
            abort_unless(User::whereKey($data['assignee_id'])->whereIn('role', [User::ROLE_KOORDINATOR_IT, User::ROLE_STAFF_IT])->exists(), 422);
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

        $ticket->update($data);
        return back()->with('success', 'Tiket ' . $ticket->kode . ' diperbarui.');
    }

    public function destroy(Request $request, ItTicket $ticket): RedirectResponse
    {
        abort_unless($request->user()->isKoordinatorIt(), 403);

        $ticket->delete();
        return back()->with('success', 'Tiket ' . $ticket->kode . ' dihapus.');
    }

    private function canManage(User $user): bool
    {
        return $user->isKoordinatorIt() || $user->isStaffIt();
    }
}
