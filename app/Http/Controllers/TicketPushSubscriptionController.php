<?php

namespace App\Http\Controllers;

use App\Models\ItTicketPushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketPushSubscriptionController extends Controller
{
    public function config(): JsonResponse
    {
        $publicKey = (string) config('webpush.public_key');

        return response()->json([
            'available' => $publicKey !== '' && (string) config('webpush.private_key') !== '',
            'publicKey' => $publicKey,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint' => ['required', 'url', 'max:2048'],
            'keys.p256dh' => ['required', 'string', 'max:255'],
            'keys.auth' => ['required', 'string', 'max:255'],
        ]);

        ItTicketPushSubscription::updateOrCreate(
            ['endpoint_hash' => hash('sha256', $data['endpoint'])],
            [
                'user_id' => $request->user()->id,
                'endpoint' => $data['endpoint'],
                'public_key' => $data['keys']['p256dh'],
                'auth_token' => $data['keys']['auth'],
            ],
        );

        return response()->json(['saved' => true]);
    }
}
