<?php

namespace App\Jobs;

use App\Models\WebPushSubscription;
use App\Services\WebPushService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWebPush implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $userId, public array $payload)
    {
        $this->onQueue('default');
    }

    public function handle(WebPushService $push): void
    {
        $push->sendToUser($this->userId, $this->payload);
    }
}
