<?php

namespace App\Http\Controllers;

use App\Models\WebPushSubscription;
use App\Services\WebPushService;
use Illuminate\Http\Request;

class WebPushController extends Controller
{
    public function publicKey(WebPushService $push)
    {
        return response()->json(['publicKey' => $push->getPublicKey()]);
    }

    public function subscribe(Request $request)
    {
        $userId = (int) $request->attributes->get('auth_user_id', 0);
        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'endpoint' => ['required', 'string', 'max:2048'],
            'keys.p256dh' => ['required', 'string'],
            'keys.auth' => ['required', 'string'],
        ]);

        $sub = WebPushSubscription::updateOrCreate(
            ['endpoint' => $data['endpoint']],
            [
                'user_id' => $userId,
                'p256dh' => $data['keys']['p256dh'],
                'auth' => $data['keys']['auth'],
                'ua' => (string) $request->header('User-Agent'),
                'ip' => $request->ip(),
                'last_used_at' => now(),
            ]
        );

        return response()->json(['id' => $sub->id]);
    }

    public function unsubscribe(Request $request)
    {
        $userId = (int) $request->attributes->get('auth_user_id', 0);
        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'endpoint' => ['required', 'string'],
        ]);

        WebPushSubscription::where('endpoint', $data['endpoint'])
            ->where('user_id', $userId)
            ->delete();

        return response()->json(['status' => 'ok']);
    }

    public function sendTest(Request $request, WebPushService $push)
    {
        $userId = (int) $request->attributes->get('auth_user_id', 0);
        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payload = $request->validate([
            'title' => ['sometimes', 'string'],
            'body' => ['sometimes', 'string'],
            'data' => ['sometimes', 'array'],
        ]);

        $message = [
            'title' => $payload['title'] ?? 'Hello',
            'body' => $payload['body'] ?? 'Test notification',
            'data' => $payload['data'] ?? [],
        ];

        $sent = $push->sendToUser($userId, $message);
        return response()->json(['sent' => $sent]);
    }
}
