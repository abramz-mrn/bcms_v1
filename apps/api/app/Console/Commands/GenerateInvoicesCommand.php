<?php

namespace App\Console\Commands;

use App\Services\BillingService;
use Illuminate\Console\Command;

class GenerateInvoicesCommand extends Command
{
    protected $signature = 'invoice:generate {--subscription= : Generate for specific subscription}';
    protected $description = 'Generate invoices for active subscriptions';

    public function handle(BillingService $billingService): int
    {
        $this->info('Starting invoice generation...');

        if ($subscriptionId = $this->option('subscription')) {
            $subscription = \App\Models\Subscription::find($subscriptionId);
            if ($subscription) {
                $invoice = $billingService->generateInvoiceForSubscription($subscription);
                $this->info("Generated invoice:  {$invoice->invoice_no}");
            } else {
                $this->error("Subscription not found: {$subscriptionId}");
                return 1;
            }
        } else {
            $count = $billingService->generateMonthlyInvoices();
            $this->info("Generated {$count} invoices");
        }

        return 0;
    }
}