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

class ProcessSoftLimitJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public Subscription $subscription
    ) {}

    public function handle(MikrotikService $mikrotikService, NotificationService $notificationService): void
    {
        if ($this->subscription->status !== 'Active') {
            return;
        }

        // Check if there's an unpaid overdue invoice
        $overdueInvoice = Invoice::where('subscription_id', $this->subscription->id)
            ->where('status', 'Unpaid')
            ->whereDate('due_date', '<', now())
            ->first();

        if (! $overdueInvoice) {
            return;
        }

        $internetService = $this->subscription->product->internetService;
        if (! $internetService) {
            return;
        }

        $daysOverdue = $overdueInvoice->getDaysOverdue();

        if ($daysOverdue >= $internetService->auto_soft_limit) {
            try {
                $mikrotikService->applySoftLimit($this->subscription);

                // Send notification
                $notificationService->send(
                    $this->subscription->customer,
                    'PRE_SOFTLIMIT',
                    [
                        'customer_name' => $this->subscription->customer->name,
                        'invoice_no' => $overdueInvoice->invoice_no,
                        'total_amount' => number_format($overdueInvoice->total_amount, 0, ',', '.'),
                        'due_date' => $overdueInvoice->due_date->format('d/m/Y'),
                    ]
                );

                Log::info("Applied soft-limit to subscription {$this->subscription->id}");
            } catch (\Exception $e) {
                Log:: error("Failed to apply soft-limit to subscription {$this->subscription->id}: " . $e->getMessage());
                throw $e;
            }
        }
    }
}