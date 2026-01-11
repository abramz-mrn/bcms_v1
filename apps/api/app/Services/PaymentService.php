<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\User;
use App\Jobs\ProcessReactivateJob;
use App\Jobs\SendNotificationJob;

class PaymentService
{
    public function verifyPayment(Payment $payment, User $verifier): Payment
    {
        $payment->update([
            'status' => 'verified',
            'verified_by' => $verifier->id,
            'verified_at' => now(),
            'paid_at' => now(),
        ]);

        // Update invoice status
        $invoice = $payment->invoice;
        $totalPaid = $invoice->payments()->where('status', 'verified')->sum('amount');

        if ($totalPaid >= $invoice->total_amount) {
            $invoice->update(['status' => 'Paid']);

            // Reactivate subscription if suspended
            $subscription = $invoice->subscription;
            if ($subscription && in_array($subscription->status, ['Soft-Limit', 'Suspend'])) {
                ProcessReactivateJob::dispatch($subscription);
            }

            // Send payment received notification
            SendNotificationJob:: dispatch(
                $invoice->customer,
                'PAYMENT_RECEIVED',
                $this->getPaymentNotificationData($payment)
            );
        } elseif ($totalPaid > 0) {
            $invoice->update(['status' => 'Partial']);
        }

        return $payment;
    }

    public function processWebhook(string $gateway, array $payload): Payment
    {
        $transactionId = $this->extractTransactionId($gateway, $payload);
        
        $payment = Payment::where('transaction_id', $transactionId)->first();

        if (!$payment) {
            throw new \Exception('Payment not found for transaction: ' . $transactionId);
        }

        $status = $this->extractPaymentStatus($gateway, $payload);

        if ($status === 'success' || $status === 'settlement') {
            $this->verifyPayment($payment, User::first()); // System verification
        } elseif ($status === 'failed' || $status === 'expired') {
            $payment->update([
                'status' => 'rejected',
                'notes' => 'Payment ' . $status .  ' via ' . $gateway,
            ]);
        }

        return $payment;
    }

    protected function extractTransactionId(string $gateway, array $payload): string
    {
        return match ($gateway) {
            'midtrans' => $payload['order_id'] ?? $payload['transaction_id'] ?? '',
            'xendit' => $payload['external_id'] ?? $payload['id'] ?? '',
            default => '',
        };
    }

    protected function extractPaymentStatus(string $gateway, array $payload): string
    {
        return match ($gateway) {
            'midtrans' => $payload['transaction_status'] ?? '',
            'xendit' => strtolower($payload['status'] ?? ''),
            default => '',
        };
    }

    protected function getPaymentNotificationData(Payment $payment): array
    {
        $invoice = $payment->invoice;
        $customer = $invoice->customer;

        return [
            'customer_name' => $customer->name,
            'invoice_no' => $invoice->invoice_no,
            'amount_paid' => number_format($payment->amount, 0, ',', '.'),
            'payment_date' => $payment->paid_at->format('d/m/Y H:i'),
            'payment_method' => ucfirst($payment->payment_method),
        ];
    }
}