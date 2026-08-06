<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Illuminate\Http\Request;

class JadwalMeetingApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Meeting::query();

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

        $meetings = $query->orderBy('date')->orderBy('start_time')->get();
        $recurring = Meeting::whereNotNull('recurring_type')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'meetings' => $meetings->map(fn (Meeting $m) => $this->serialize($m)),
                'recurring' => $recurring->map(fn (Meeting $m) => $this->serialize($m)),
            ],
        ]);
    }

    private function serialize(Meeting $meeting): array
    {
        return [
            'id' => $meeting->id,
            'title' => $meeting->title,
            'room' => $meeting->room,
            'team' => $meeting->team,
            'date' => $meeting->date?->toDateString(),
            'start_time' => $meeting->start_time,
            'end_time' => $meeting->end_time,
            'actual_end_time' => $meeting->actual_end_time?->toDateTimeString(),
            'status' => $meeting->status,
            'description' => $meeting->description,
            'recurring_type' => $meeting->recurring_type,
            'recurring_day' => $meeting->recurring_day,
            'creator' => [
                'name' => $meeting->creator?->name ?? '-',
            ],
        ];
    }
}
