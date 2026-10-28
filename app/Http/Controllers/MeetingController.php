<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Services\MeetingApiService;
use Carbon\Carbon;
use App\Services\ExternalMeetingService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Throwable;

class MeetingController extends Controller
{
    public function jadwal(Request $request, ExternalMeetingService $externalMeetingService)
    {
        $user = auth()->user();
        $isAdvancedView = $user->isGmCeo() || $user->isManager() || $user->isSuperAdminLike();

        $month = (int) ($request->month ?? now()->month);
        $year = (int) ($request->year ?? now()->year);

        $view = $request->view;
        if (!in_array($view, ['month', 'week', 'day'], true)) {
            $view = 'month';
        }
        if (!$isAdvancedView && in_array($view, ['week', 'day'], true)) {
            $view = 'month';
        }

        $focus = $request->date ? \Carbon\Carbon::parse($request->date) : now();
        $weekStart = $focus->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
        $weekEnd = $focus->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);

        $meetings = Meeting::with('creator')->orderBy('start_time')
            ->where(function ($q) use ($view, $month, $year, $weekStart, $weekEnd, $focus) {
                if ($view === 'week') {
                    $q->whereBetween('date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')]);
                } elseif ($view === 'day') {
                    $q->whereDate('date', $focus->format('Y-m-d'));
                } else {
                    $q->whereMonth('date', $month)->whereYear('date', $year);
                }
            })
            ->orWhereNotNull('recurring_type')
            ->get();

        $external = $externalMeetingService->fetch();
        $externalMeetings = $external->filter(function ($m) use ($view, $month, $year, $weekStart, $weekEnd, $focus) {
            if ($m->recurring_type || $m->recurring_day) return true;
            if (!$m->date) return false;
            if ($view === 'week') return $m->date->between($weekStart, $weekEnd);
            if ($view === 'day') return $m->date->isSameDay($focus);
            return $m->date->month === $month && $m->date->year === $year;
        });

        $localSignatures = $meetings->map(fn ($m) => $this->meetingSignature($m))->toArray();

        $externalMeetings = $externalMeetings->reject(
            fn ($m) => in_array($this->meetingSignature($m), $localSignatures, true)
        );

        $meetings = $meetings->concat($externalMeetings);

        $meetings = $meetings->map(function ($m) {
            $display = $m->status ?? 'booked';

            if (!$m->recurring_type) {
                if (in_array($display, ['cancelled', 'completed'], true)) {
                    // keep explicit status
                } elseif ($m->actual_end_time) {
                    $display = 'completed';
                } elseif ($m->date && $m->end_time) {
                    $meetingEnd = \Carbon\Carbon::parse($m->date->toDateString() . ' ' . $m->end_time);
                    if ($meetingEnd->isPast()) {
                        $display = 'completed';
                    } elseif ($m->start_time && \Carbon\Carbon::parse($m->date->toDateString() . ' ' . $m->start_time)->isPast()) {
                        $display = 'ongoing';
                    }
                }
            }

            $m->display_status = $display;

            return $m;
        });

        $recurring = $meetings->whereNotNull('recurring_type');
        $nonRecurring = $meetings->whereNull('recurring_type');

        return view('meeting.jadwal', compact(
            'meetings', 'recurring', 'nonRecurring', 'month', 'year', 'view',
            'isAdvancedView', 'focus', 'weekStart', 'weekEnd'
        ));
    }

    private function meetingSignature(object $m): string
    {
        $date = $m->date;
        $dateStr = $date
            ? ($date instanceof \Carbon\Carbon ? $date->format('Y-m-d') : (string) $date)
            : 'recurring';

        $time = (string) ($m->start_time ?? '');
        if (preg_match('/(\d{1,2}):(\d{2})/', $time, $matches)) {
            $time = sprintf('%02d:%02d', (int) $matches[1], (int) $matches[2]);
        }

        return strtolower((string) $m->title) . '|' . $dateStr . '|' . $time;
    }

    public function permintaan(Request $request, MeetingApiService $meetingApi)
    {
        $user = auth()->user();
        $canReview = $user->canViewAll();
        $apiError = null;
        $requests = new LengthAwarePaginator(collect(), 0, 10, LengthAwarePaginator::resolveCurrentPage(), [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        try {
            $filters = array_filter([
                'status' => $request->input('status'),
                'search' => $request->input('search'),
                'requester_username' => $canReview ? null : $user->username,
                'per_page' => 100,
                'page' => max(1, (int) $request->input('page', 1)),
            ], fn ($value) => $value !== null && $value !== '');
            $payload = $meetingApi->requests($filters);
            $pagination = data_get($payload, 'pagination', []);
            $items = collect(data_get($payload, 'data', []))->map(fn (array $item) => (object) [
                ...$item,
                'date' => ! empty($item['date']) ? Carbon::parse($item['date']) : null,
                'start_time' => $item['start_time'] ?? null,
                'end_time' => $item['end_time'] ?? null,
                'employee' => (object) ['nama' => data_get($item, 'employee.nama', data_get($item, 'employee.name', '-'))],
                'approver' => (object) ['name' => data_get($item, 'approver.name', '-')],
            ]);
            $requests = new LengthAwarePaginator(
                $items,
                (int) data_get($pagination, 'total', $items->count()),
                (int) data_get($pagination, 'per_page', 100),
                (int) data_get($pagination, 'current_page', 1),
                ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
            );
        } catch (Throwable $exception) {
            report($exception);
            $apiError = 'Data meeting belum dapat dimuat dari server meeting. '.$exception->getMessage();
        }

        return view('meeting.permintaan', compact('requests', 'apiError', 'canReview'));
    }

    public function storePermintaan(Request $request, MeetingApiService $meetingApi)
    {
        abort_unless(auth()->user()->isStaff(), 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'room' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
            'why' => 'nullable|string',
            'what' => 'nullable|string',
            'how' => 'nullable|string',
        ]);

        $username = auth()->user()->username;
        if (! $username) {
            return back()->withInput()->with('error', 'Akun Anda belum memiliki username untuk dihubungkan ke sistem meeting.');
        }

        try {
            $meetingApi->submit([...$validated, 'requester_username' => $username]);
        } catch (Throwable $exception) {
            report($exception);

            return back()->withInput()->with('error', 'Pengajuan belum terkirim: '.$exception->getMessage());
        }

        return redirect()->route('meeting.permintaan')->with('success', 'Permintaan meeting berhasil dikirim.');
    }

    public function setujui(int $meetingRequest, MeetingApiService $meetingApi)
    {
        abort_unless(auth()->user()->canViewAll(), 403);

        try {
            $meetingApi->approve($meetingRequest);
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('error', 'Persetujuan belum tersimpan: '.$exception->getMessage());
        }

        return back()->with('success', 'Permintaan meeting disetujui.');
    }

    public function tolak(Request $request, int $meetingRequest, MeetingApiService $meetingApi)
    {
        abort_unless(auth()->user()->canViewAll(), 403);
        $validated = $request->validate(['notes' => 'required|string|max:10000']);

        try {
            $meetingApi->reject($meetingRequest, $validated['notes']);
        } catch (Throwable $exception) {
            report($exception);

            return back()->withInput()->with('error', 'Penolakan belum tersimpan: '.$exception->getMessage());
        }

        return back()->with('success', 'Permintaan meeting ditolak.');
    }
}
