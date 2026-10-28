<?php

namespace App\Http\Controllers;

use App\Events\ChatCallSignal;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatCallSignalController extends Controller
{
    public function store(Request $request, User $recipient): JsonResponse
    {
        abort_unless((int) $recipient->id !== (int) $request->user()->id, 404);
        $data = $request->validate([
            'type' => ['required', 'in:offer,answer,ice,end,reject'],
            'call_id' => ['required', 'uuid'],
            'mode' => ['required', 'in:audio,video'],
            'payload' => ['sometimes', 'array'],
            'payload.sdp' => ['sometimes', 'array'],
            'payload.candidate' => ['sometimes', 'array'],
        ]);

        $sender = $request->user()->loadMissing('employee');
        ChatCallSignal::dispatch(
            (int) $recipient->id,
            (int) $sender->id,
            $sender->employee?->nama ?? $sender->name,
            $data['type'],
            $data['call_id'],
            $data['mode'],
            $data['payload'] ?? [],
        );

        return response()->json(['sent' => true]);
    }
}
