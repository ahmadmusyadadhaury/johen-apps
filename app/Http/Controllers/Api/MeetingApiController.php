<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MeetingApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Meeting::with('creator');

        if ($request->date) {
            $query->whereDate('date', $request->date);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->month && $request->year) {
            $query->whereMonth('date', $request->month)
                  ->whereYear('date', $request->year);
        }

        if ($request->room) {
            $query->where('room', $request->room);
        }

        $recurring = Meeting::whereNotNull('recurring_type')->get();
        $nonRecurring = $query->orderBy('date')->orderBy('start_time')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'recurring' => $recurring,
                'meetings' => $nonRecurring,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'nullable|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'room' => 'required|string|max:255',
            'recurring_type' => 'nullable|in:weekly,biweekly,monthly',
            'recurring_day' => 'nullable|string|max:20',
            'team' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:booked,ongoing,queue',
        ]);

        $validated['created_by'] = $request->user()->id;
        $meeting = Meeting::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Meeting berhasil dibuat',
            'data' => $meeting->load('creator'),
        ], 201);
    }

    public function show(Meeting $meeting)
    {
        return response()->json([
            'success' => true,
            'data' => $meeting->load('creator'),
        ]);
    }

    public function update(Request $request, Meeting $meeting)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'nullable|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'room' => 'required|string|max:255',
            'recurring_type' => 'nullable|in:weekly,biweekly,monthly',
            'recurring_day' => 'nullable|string|max:20',
            'team' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:booked,ongoing,queue,completed,cancelled',
        ]);

        $meeting->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Meeting berhasil diupdate',
            'data' => $meeting->load('creator'),
        ]);
    }

    public function destroy(Meeting $meeting)
    {
        $meeting->delete();

        return response()->json([
            'success' => true,
            'message' => 'Meeting berhasil dihapus',
        ]);
    }

    public function updateStatus(Request $request, Meeting $meeting)
    {
        $validated = $request->validate([
            'status' => 'required|in:booked,ongoing,queue,completed,cancelled',
            'actual_end_time' => 'nullable|date',
        ]);

        $meeting->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Status meeting berhasil diupdate',
            'data' => $meeting->load('creator'),
        ]);
    }

    public function meetingRequests(Request $request)
    {
        $query = MeetingRequest::with(['employee', 'approver']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('requester_username')) {
            $employeeId = User::where('username', $request->string('requester_username'))->value('employee_id');
            $query->where('employee_id', $employeeId ?? 0);
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->trim()->toString();
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('room', 'like', "%{$search}%")
                    ->orWhereHas('employee', fn ($employee) => $employee->where('nama', 'like', "%{$search}%"));
            });
        }

        $requests = $query->latest()->paginate(min(100, max(1, (int) $request->input('per_page', 100))));

        return response()->json([
            'success' => true,
            'data' => $requests->items(),
            'pagination' => [
                'total' => $requests->total(),
                'per_page' => $requests->perPage(),
                'current_page' => $requests->currentPage(),
                'last_page' => $requests->lastPage(),
            ],
        ]);
    }

    public function storeMeetingRequest(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'room' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'why' => 'nullable|string',
            'what' => 'nullable|string',
            'how' => 'nullable|string',
            'employee_id' => 'nullable|exists:employees,id',
            'requester_username' => 'required_without:employee_id|string|exists:users,username',
        ]);

        if (! empty($validated['requester_username'])) {
            $validated['employee_id'] = User::where('username', $validated['requester_username'])->value('employee_id');
            unset($validated['requester_username']);
        }

        if (empty($validated['employee_id'])) {
            return response()->json(['success' => false, 'message' => 'Akun pemohon belum terhubung ke data karyawan.'], 422);
        }

        $meetingRequest = MeetingRequest::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan meeting berhasil diajukan',
            'data' => $meetingRequest->load('employee'),
        ], 201);
    }

    public function approveMeetingRequest(Request $request, MeetingRequest $meetingRequest)
    {
        $processed = DB::transaction(function () use ($meetingRequest, $request) {
            $lockedRequest = MeetingRequest::query()->lockForUpdate()->findOrFail($meetingRequest->id);
            if ($lockedRequest->status !== 'pending') {
                return false;
            }

            $lockedRequest->update([
                'status' => 'disetujui',
                'approved_by' => $request->user()->id,
                'notes' => $request->input('notes'),
            ]);

            Meeting::create([
                'title' => $lockedRequest->title,
                'date' => $lockedRequest->date,
                'start_time' => $lockedRequest->start_time,
                'end_time' => $lockedRequest->end_time,
                'room' => $lockedRequest->room,
                'status' => 'booked',
                'description' => collect([
                    $lockedRequest->why ? 'Why: '.$lockedRequest->why : null,
                    $lockedRequest->what ? 'What: '.$lockedRequest->what : null,
                    $lockedRequest->how ? 'How: '.$lockedRequest->how : null,
                ])->filter()->implode("\n"),
                'created_by' => $request->user()->id,
            ]);

            return true;
        });

        if (! $processed) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan sudah diproses sebelumnya',
            ], 409);
        }

        return response()->json([
            'success' => true,
            'message' => 'Permintaan meeting disetujui',
            'data' => $meetingRequest->refresh()->load(['employee', 'approver']),
        ]);
    }

    public function rejectMeetingRequest(Request $request, MeetingRequest $meetingRequest)
    {
        $validated = $request->validate([
            'notes' => 'required|string',
        ]);

        $processed = DB::transaction(function () use ($meetingRequest, $request, $validated) {
            $lockedRequest = MeetingRequest::query()->lockForUpdate()->findOrFail($meetingRequest->id);
            if ($lockedRequest->status !== 'pending') {
                return false;
            }

            $lockedRequest->update([
                'status' => 'ditolak',
                'approved_by' => $request->user()->id,
                'notes' => $validated['notes'],
            ]);

            return true;
        });

        if (! $processed) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan sudah diproses sebelumnya',
            ], 409);
        }

        return response()->json([
            'success' => true,
            'message' => 'Permintaan meeting ditolak',
            'data' => $meetingRequest->refresh()->load(['employee', 'approver']),
        ]);
    }
}
