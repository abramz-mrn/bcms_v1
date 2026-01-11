<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Services\BillingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public Subscription $subscription
    ) {}

    public function handle(BillingService $billingService): void
    {
        try {
            $invoice = $billingService->generateInvoiceForSubscription($this->subscription);
            
            Log::info("Generated invoice {$invoice->invoice_no} for subscription {$this->subscription->id}");
        } catch (\Exception $e) {
            Log::error("Failed to generate invoice for subscription {$this->subscription->id}: " . $e->getMessage());
            throw $e;
        }
    }
}