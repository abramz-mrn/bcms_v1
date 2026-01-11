<?php

namespace App\Jobs;

use App\Models\Reminder;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 300;

    public function __construct(
        public Reminder $reminder
    ) {}

    public function handle(NotificationService $notificationService): void
    {
        if ($this->reminder->status !== 'pending') {
            return;
        }

        // Check if invoice is already paid
        if ($this->reminder->invoice->isPaid()) {
            $this->reminder->update(['status' => 'cancelled']);
            return;
        }

        try {
            $notificationService->sendReminder($this->reminder);
            
            Log::info("Sent reminder {$this->reminder->id} for invoice {$this->reminder->invoice->invoice_no}");
        } catch (\Exception $e) {
            Log::error("Failed to send reminder {$this->reminder->id}: " . $e->getMessage());
            throw $e;
        }
    }
}