<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DesktopNotification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DesktopNotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $token = trim((string) $request->query('token', ''));
        $after = max(0, (int) $request->query('after', 0));

        if ($token === '') {
            return response()->json(['message' => 'Token wajib diisi.'], 422);
        }

        $user = User::where('desktop_token', $token)->with('employee')->first();

        if (! $user || ! $user->employee) {
            return response()->json(['message' => 'Token tidak valid.'], 401);
        }

        $notifications = DesktopNotification::where('employee_id', $user->employee_id)
            ->where('id', '>', $after)
            ->orderBy('id', 'asc')
            ->get(['id', 'title', 'body', 'created_at']);

        return response()->json([
            'notifications' => $notifications->map(fn ($n) => [
                'id' => $n->id,
                'title' => $n->title,
                'body' => $n->body,
                'created_at' => $n->created_at?->toIso8601String(),
            ]),
            'latest_id' => $notifications->max('id') ?? $after,
        ]);
    }
}