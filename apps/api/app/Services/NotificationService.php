<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Template;
use App\Models\Reminder;
use App\Models\Company;
use App\Models\Brand;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function send(Customer $customer, string $templateCode, array $data, ? string $channel = null): bool
    {
        $channels = $channel ? [$channel] : ['email', 'sms', 'whatsapp'];
        $success = false;

        foreach ($channels as $ch) {
            $template = Template::where('code', $templateCode)
                ->where('type', $ch)
                ->where('is_active', true)
                ->first();

            if (!$template) {
                continue;
            }

            // Merge company data
            $company = Company::first();
            $brand = Brand::first();
            
            $data = array_merge($data, [
                'company_name' => $company? ->name ??  'BCMS',
                'company_phone' => $company?->phone ??  '',
                'brand_name' => $brand?->name ?? 'BCMS',
                'bank_name' => $company?->bank_account['bank_name'] ?? '',
                'bank_account_number' => $company?->bank_account['account_number'] ??  '',
                'bank_account_name' => $company?->bank_account['account_name'] ??  '',
            ]);

            try {
                $result = match ($ch) {
                    'email' => $this->sendEmail($customer, $template, $data),
                    'sms' => $this->sendSMS($customer, $template, $data),
                    'whatsapp' => $this->sendWhatsApp($customer, $template, $data),
                    default => false,
                };

                if ($result) {
                    $success = true;
                }
            } catch (\Exception $e) {
                Log::error("Failed to send {$ch} notification: " . $e->getMessage());
            }
        }

        return $success;
    }

    public function sendReminder(Reminder $reminder): bool
    {
        $invoice = $reminder->invoice;
        $customer = $invoice->customer;
        $template = $reminder->template;

        $data = [
            'customer_name' => $customer->name,
            'invoice_no' => $invoice->invoice_no,
            'total_amount' => number_format($invoice->total_amount, 0, ',', '.'),
            'due_date' => $invoice->due_date->format('d/m/Y'),
            'period_start' => $invoice->period_start->format('d/m/Y'),
            'period_end' => $invoice->period_end->format('d/m/Y'),
        ];

        // Add days remaining info
        $daysUntilDue = now()->diffInDays($invoice->due_date, false);
        if ($daysUntilDue > 0) {
            $data['days_remaining'] = "dalam {$daysUntilDue} hari";
        } elseif ($daysUntilDue === 0) {
            $data['days_remaining'] = "hari ini";
        } else {
            $data['days_remaining'] = abs($daysUntilDue) . " hari yang lalu";
        }

        try {
            $result = match ($reminder->channel) {
                'email' => $this->sendEmail($customer, $template, $data),
                'sms' => $this->sendSMS($customer, $template, $data),
                'whatsapp' => $this->sendWhatsApp($customer, $template, $data),
                default => false,
            };

            if ($result) {
                $reminder->markAsSent();
                return true;
            }

            $reminder->markAsFailed('Failed to send notification');
            return false;
        } catch (\Exception $e) {
            $reminder->markAsFailed($e->getMessage());
            return false;
        }
    }

    protected function sendEmail(Customer $customer, Template $template, array $data): bool
    {
        if (!$customer->email) {
            return false;
        }

        $subject = $template->renderSubject($data);
        $content = $template->render($data);

        Mail::raw($content, function ($message) use ($customer, $subject) {
            $message->to($customer->email, $customer->name)
                ->subject($subject);
        });

        return true;
    }

    protected function sendSMS(Customer $customer, Template $template, array $data): bool
    {
        if (!$customer->phone) {
            return false;
        }

        $content = $template->render($data);
        $phone = $this->formatPhoneNumber($customer->phone);

        // Send via SMS Gateway
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.sms. api_key'),
        ])->post(config('services. sms.url'), [
            'to' => $phone,
            'message' => $content,
        ]);

        return $response->successful();
    }

    protected function sendWhatsApp(Customer $customer, Template $template, array $data): bool
    {
        if (!$customer->phone) {
            return false;
        }

        $content = $template->render($data);
        $phone = $this->formatPhoneNumber($customer->phone, true);

        // Send via WhatsApp Business API
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.whatsapp.access_token'),
            'Content-Type' => 'application/json',
        ])->post(config('services.whatsapp.api_url') . '/' . config('services.whatsapp.phone_number_id') . '/messages', [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'text',
            'text' => [
                'body' => $content,
            ],
        ]);

        return $response->successful();
    }

    protected function formatPhoneNumber(string $phone, bool $withCountryCode = false): string
    {
        // Remove non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Convert 08xxx to 628xxx
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        // Add country code if not present
        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $withCountryCode ? $phone : $phone;
    }
}