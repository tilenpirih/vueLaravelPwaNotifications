<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use App\Models\PushSubscription;
use Illuminate\Support\Facades\Auth;

class PushController extends Controller
{
    public function subscribe(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'endpoint' => 'required|string',
            'keys.auth' => 'required|string',
            'keys.p256dh' => 'required|string',
        ]);
        PushSubscription::updateOrCreate(
            [
                'user_id' => $user->id,
                'endpoint' => $data['endpoint'],
            ],
            [
                'p256dh' => $data['keys']['p256dh'],
                'auth' => $data['keys']['auth'],
            ]
        );
        return response()->json(['success' => true]);
    }

    public function publicKey()
    {
        return response()->json([
            'publicKey' => env('VAPID_PUBLIC_KEY'),
        ]);
    }

    public function notify(Request $request)
    {
        $user = Auth::user();
        $payload = $request->input('payload', 'Hello from Laravel!');
        $auth = [
            'VAPID' => [
                'subject' => env('VAPID_SUBJECT'),
                'publicKey' => env('VAPID_PUBLIC_KEY'),
                'privateKey' => env('VAPID_PRIVATE_KEY'),
            ],
        ];
        $webPush = new WebPush($auth);
        $subs = PushSubscription::where('user_id', $user->id)->get();
        foreach ($subs as $sub) {
            $subscription = Subscription::create([
                'endpoint' => $sub->endpoint,
                'publicKey' => $sub->p256dh,
                'authToken' => $sub->auth,
            ]);
            $webPush->queueNotification($subscription, $payload);
        }
        $webPush->flush();
        return response()->json(['success' => true]);
    }
}
