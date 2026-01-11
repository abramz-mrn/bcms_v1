<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Models\Invoice;
use App\Services\MikrotikService;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSuspendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public Subscription $subscription
    ) {}

    public function handle(MikrotikService $mikrotikService, NotificationService $notificationService): void
    {
        if (! in_array($this->subscription->status, ['Active', 'Soft-Limit'])) {
            return;
        }

        $overdueInvoice = Invoice::where('subscription_id', $this->subscription->id)
            ->where('status', 'Unpaid')
            ->whereDate('due_date', '<', now())
            ->first();

        if (!$overdueInvoice) {
            return;
        }

        $internetService = $this->subscription->product->internetService;
        if (!$internetService) {
            return;
        }

        $daysOverdue = $overdueInvoice->getDaysOverdue();

        if ($daysOverdue >= $internetService->auto_suspend) {
            try {
                $mikrotikService->applySuspend($this->subscription);

                // Send notification
                $notificationService->send(
                    $this->subscription->customer,
                    'PRE_SUSPEND',
                    [
                        'customer_name' => $this->subscription->customer->name,
                        'invoice_no' => $overdueInvoice->invoice_no,
                        'total_amount' => number_format($overdueInvoice->total_amount, 0, ',', '.'),
                        'due_date' => $overdueInvoice->due_date->format('d/m/Y'),
                    ]
                );

                Log::info("Suspended subscription {$this->subscription->id}");
            } catch (\Exception $e) {
                Log:: error("Failed to suspend subscription {$this->subscription->id}: " .  $e->getMessage());
                throw $e;
            }
        }
    }
}