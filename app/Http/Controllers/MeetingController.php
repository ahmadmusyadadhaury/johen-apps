<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingRequest;
use App\Services\ExternalMeetingService;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function jadwal(Request $request, ExternalMeetingService $externalMeetingService)
    {
        $user = auth()->user();
        $isAdvancedView = $user->isGmCeo() || $user->isManager() || $user->isSuperAdmin();

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

        $meetings = $meetings->merge($externalMeetings);

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

    public function permintaan(Request $request)
    {
        $user = auth()->user();
        $employee = $user->employee;

        if ($user->isStaff()) {
            $requests = MeetingRequest::with('employee')
                ->where('employee_id', $employee?->id)
                ->latest()
                ->paginate(10);
        } else {
            $requests = MeetingRequest::with(['employee', 'approver'])
                ->when($request->status, function ($q, $status) {
                    $q->where('status', $status);
                })
                ->when($request->search, function ($q, $search) {
                    $q->where(function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                            ->orWhere('room', 'like', "%{$search}%")
                            ->orWhereHas('employee', function ($q) use ($search) {
                                $q->where('nama', 'like', "%{$search}%");
                            });
                    });
                })
                ->latest()
                ->paginate(10);
        }

        return view('meeting.permintaan', compact('requests'));
    }

    public function storePermintaan(Request $request)
    {
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

        $employee = auth()->user()->employee;

        if (!$employee) {
            return back()->with('error', 'Akun Anda tidak terhubung ke data karyawan.');
        }

        MeetingRequest::create(array_merge($validated, [
            'employee_id' => $employee->id,
        ]));

        return redirect()->route('meeting.permintaan')->with('success', 'Permintaan meeting berhasil dikirim.');
    }

    public function setujui(MeetingRequest $meetingRequest)
    {
        $meetingRequest->update([
            'status' => 'disetujui',
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'Permintaan meeting disetujui.');
    }

    public function tolak(Request $request, MeetingRequest $meetingRequest)
    {
        $meetingRequest->update([
            'status' => 'ditolak',
            'approved_by' => auth()->id(),
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Permintaan meeting ditolak.');
    }
}
