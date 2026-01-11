<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 300;

    public function __construct(
        public Customer $customer,
        public string $templateCode,
        public array $data,
        public ? string $channel = null
    ) {}

    public function handle(NotificationService $notificationService): void
    {
        try {
            $notificationService->send(
                $this->customer,
                $this->templateCode,
                $this->data,
                $this->channel
            );

            Log::info("Sent notification {$this->templateCode} to customer {$this->customer->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send notification:  " . $e->getMessage());
            throw $e;
        }
    }
}