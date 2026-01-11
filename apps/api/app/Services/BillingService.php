<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Subscription;
use App\Models\Reminder;
use App\Models\Template;
use Carbon\Carbon;

class BillingService
{
    public function generateInvoiceForSubscription(Subscription $subscription): Invoice
    {
        $product = $subscription->product;
        $customer = $subscription->customer;

        $periodStart = Carbon::now()->startOfMonth();
        $periodEnd = Carbon::now()->endOfMonth();

        // Check for active promotion
        $promotion = $product->getActivePromotion();
        $discountAmount = $promotion ? $promotion->discount :  0;

        // Calculate amounts
        $baseAmount = $product->price;
        
        if ($product->tax_included) {
            // Price includes tax, calculate base amount
            $taxRate = $product->tax_rate / 100;
            $amount = $baseAmount / (1 + $taxRate);
            $taxAmount = $baseAmount - $amount;
        } else {
            // Price excludes tax
            $amount = $baseAmount;
            $taxAmount = $amount * ($product->tax_rate / 100);
        }

        $totalAmount = $amount + $taxAmount - $discountAmount;

        // Determine due date based on billing cycle
        $dueDate = $this->calculateDueDate($subscription);

        $invoice = Invoice::create([
            'invoice_no' => Invoice::generateInvoiceNo(),
            'customer_id' => $customer->id,
            'subscription_id' => $subscription->id,
            'product_id' => $product->id,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'amount' => round($amount, 2),
            'tax_amount' => round($taxAmount, 2),
            'discount_amount' => round($discountAmount, 2),
            'total_amount' => round($totalAmount, 2),
            'due_date' => $dueDate,
            'status' => 'Unpaid',
            'created_by' => auth()->id(),
        ]);

        // Create reminders
        $this->createRemindersForInvoice($invoice);

        return $invoice;
    }

    public function generateMonthlyInvoices(): int
    {
        $count = 0;
        $targetDate = Carbon::now()->addDays(7); // Generate invoices 7 days before period start

        $subscriptions = Subscription::with(['product', 'customer'])
            ->where('status', 'Active')
            ->whereHas('product', function ($query) {
                $query->where('billing_cycle', 'Monthly');
            })
            ->get();

        foreach ($subscriptions as $subscription) {
            // Check if invoice already exists for this period
            $existingInvoice = Invoice::where('subscription_id', $subscription->id)
                ->whereMonth('period_start', $targetDate->month)
                ->whereYear('period_start', $targetDate->year)
                ->exists();

            if (!$existingInvoice) {
                $this->generateInvoiceForSubscription($subscription);
                $count++;
            }
        }

        return $count;
    }

    public function createRemindersForInvoice(Invoice $invoice): void
    {
        $subscription = $invoice->subscription;
        $customer = $subscription->customer;

        $reminderConfigs = [
            ['trigger_type' => 'before_due', 'days_offset' => -7, 'template_code' => 'REMINDER_H7'],
            ['trigger_type' => 'before_due', 'days_offset' => -3, 'template_code' => 'REMINDER_H3'],
            ['trigger_type' => 'before_due', 'days_offset' => -1, 'template_code' => 'REMINDER_H1'],
            ['trigger_type' => 'after_due', 'days_offset' => 1, 'template_code' => 'REMINDER_H1'],
            ['trigger_type' => 'pre_soft_limit', 'days_offset' => null, 'template_code' => 'PRE_SOFTLIMIT'],
            ['trigger_type' => 'pre_suspend', 'days_offset' => null, 'template_code' => 'PRE_SUSPEND'],
        ];

        foreach ($reminderConfigs as $config) {
            // Determine channels based on customer consent
            $channels = [];
            if ($customer && $subscription->email_consent && $customer->email) {
                $channels[] = 'email';
            }
            if ($customer && $subscription->sms_consent && $customer->phone) {
                $channels[] = 'sms';
            }
            if ($customer && $subscription->whatsapp_consent && $customer->phone) {
                $channels[] = 'whatsapp';
            }

            foreach ($channels as $channel) {
                $template = Template::where('code', $config['template_code'])
                    ->where('type', $channel)
                    ->where('is_active', true)
                    ->first();

                if (! $template) {
                    continue;
                }

                // Calculate scheduled date
                if ($config['days_offset'] !== null) {
                    $scheduledAt = $invoice->due_date->copy()->addDays($config['days_offset']);
                } else {
                    // For soft-limit and suspend, calculate from internet service settings
                    $internetService = $subscription->product->internetService;
                    if ($internetService) {
                        if ($config['trigger_type'] === 'pre_soft_limit') {
                            $days = $internetService->auto_soft_limit - 1;
                        } else {
                            $days = $internetService->auto_suspend - 1;
                        }
                        $scheduledAt = $invoice->due_date->copy()->addDays($days);
                    } else {
                        continue;
                    }
                }

                // Don't create reminders for past dates
                if ($scheduledAt->isPast()) {
                    continue;
                }

                Reminder::create([
                    'invoice_id' => $invoice->id,
                    'template_id' => $template->id,
                    'channel' => $channel,
                    'trigger_type' => $config['trigger_type'],
                    'days_offset' => $config['days_offset'] ?? 0,
                    'scheduled_at' => $scheduledAt,
                    'status' => 'pending',
                ]);
            }
        }
    }

    protected function calculateDueDate(Subscription $subscription): Carbon
    {
        $registrationDay = $subscription->registration_date->day;
        $dueDate = Carbon::now()->startOfMonth()->addDays($registrationDay - 1);
        
        // If due date has passed this month, set it to next month
        if ($dueDate->isPast()) {
            $dueDate->addMonth();
        }

        return $dueDate;
    }
}