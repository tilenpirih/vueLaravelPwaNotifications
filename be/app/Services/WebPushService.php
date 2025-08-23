<?php

namespace App\Services;

use App\Models\WebPushSubscription;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    public function __construct(private readonly array $config = [])
    {
    }

    protected function vapid(): array
    {
        return [
            'VAPID' => [
                'subject' => config('webpush.vapid.subject'),
                'publicKey' => config('webpush.vapid.public_key'),
                'privateKey' => config('webpush.vapid.private_key'),
            ],
        ];
    }

    public function getPublicKey(): string
    {
        return (string) config('webpush.vapid.public_key');
    }

    public function sendToSubscription(WebPushSubscription $sub, array $payload): bool
    {
        $webPush = new WebPush($this->vapid());

        $subscription = Subscription::create([
            'endpoint' => $sub->endpoint,
            'publicKey' => $sub->p256dh,
            'authToken' => $sub->auth,
        ]);

        $report = $webPush->sendOneNotification($subscription, json_encode($payload));

        if ($report->isSuccess()) {
            $sub->forceFill(['last_used_at' => now()])->save();
            return true;
        }

        // Remove gone subscriptions
        if ($report->isSubscriptionExpired() || $report->getResponse()?->getStatusCode() === 410) {
            $sub->delete();
        }

        return false;
    }

    public function sendToUser(int $userId, array $payload): int
    {
        $count = 0;
        WebPushSubscription::where('user_id', $userId)->get()
            ->each(function (WebPushSubscription $s) use (&$count, $payload) {
                if ($this->sendToSubscription($s, $payload)) {
                    $count++;
                }
            });
        return $count;
    }
}
