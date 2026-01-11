<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Services\MikrotikService;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessReactivateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public Subscription $subscription
    ) {}

    public function handle(MikrotikService $mikrotikService, NotificationService $notificationService): void
    {
        if (!in_array($this->subscription->status, ['Soft-Limit', 'Suspend'])) {
            return;
        }

        try {
            $mikrotikService->applyReactivate($this->subscription);

            // Send notification
            $notificationService->send(
                $this->subscription->customer,
                'PAYMENT_RECEIVED',
                [
                    'customer_name' => $this->subscription->customer->name,
                ]
            );

            Log::info("Reactivated subscription {$this->subscription->id}");
        } catch (\Exception $e) {
            Log::error("Failed to reactivate subscription {$this->subscription->id}: " .  $e->getMessage());
            throw $e;
        }
    }
}