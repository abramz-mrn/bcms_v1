<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Jobs\ProcessSuspendJob;
use Illuminate\Console\Command;

class CheckSuspendCommand extends Command
{
    protected $signature = 'subscription:check-suspend';
    protected $description = 'Check and suspend overdue subscriptions';

    public function handle(): int
    {
        $this->info('Checking for suspend candidates...');

        $subscriptions = Subscription::whereIn('status', ['Active', 'Soft-Limit'])
            ->whereHas('invoices', function ($query) {
                $query->where('status', 'Unpaid')
                    ->whereDate('due_date', '<', now());
            })
            ->with(['product.internetService', 'provisioning.router'])
            ->get();

        $count = 0;
        foreach ($subscriptions as $subscription) {
            $internetService = $subscription->product->internetService;
            if (!$internetService) {
                continue;
            }

            $overdueInvoice = $subscription->invoices()
                ->where('status', 'Unpaid')
                ->whereDate('due_date', '<', now())
                ->first();

            if ($overdueInvoice) {
                $daysOverdue = $overdueInvoice->getDaysOverdue();
                
                if ($daysOverdue >= $internetService->auto_suspend) {
                    ProcessSuspendJob::dispatch($subscription);
                    $count++;
                }
            }
        }

        $this->info("Dispatched {$count} suspend jobs");

        return 0;
    }
}