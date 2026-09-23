<?php

namespace App\Events;

use App\Models\WeeklyMeetingAttendance;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WeeklyMeetingAttendanceCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $meetingId;
    public int $attendanceId;
    public string $employeeName;
    public string $attendedAt;

    /**
     * Create a new event instance.
     */
    public function __construct(WeeklyMeetingAttendance $attendance)
    {
        $this->meetingId = $attendance->weekly_meeting_id;
        $this->attendanceId = $attendance->id;
        $this->employeeName = $attendance->employee?->nama ?? 'Unknown';
        $this->attendedAt = $attendance->attended_at->format('H:i:s');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("weekly-meeting.{$this->meetingId}"),
            new PrivateChannel('weekly-meeting.admin'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'WeeklyMeetingAttendanceCreated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'meeting_id' => $this->meetingId,
            'attendance_id' => $this->attendanceId,
            'employee_name' => $this->employeeName,
            'attended_at' => $this->attendedAt,
        ];
    }
}