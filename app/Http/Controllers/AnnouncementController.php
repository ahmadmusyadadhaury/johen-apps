<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\JsonResponse;

class AnnouncementController extends Controller
{
    public function markRead(Announcement $announcement): JsonResponse
    {
        $user = auth()->user();

        $user->readAnnouncements()->syncWithoutDetaching([
            $announcement->id => ['read_at' => now()],
        ]);

        return response()->json(['ok' => true]);
    }
}
