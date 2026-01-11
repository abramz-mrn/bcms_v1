<?php

namespace App\Console\Commands;

use App\Models\Reminder;
use App\Jobs\SendReminderJob;
use Illuminate\Console\Command;

class ProcessRemindersCommand extends Command
{
    protected $signature = 'reminder:process';
    protected $description = 'Process and send pending reminders';

    public function handle(): int
    {
        $this->info('Processing reminders...');

        $reminders = Reminder::where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->with(['invoice', 'template'])
            ->get();

        $count = 0;
        foreach ($reminders as $reminder) {
            // Skip if invoice is already paid
            if ($reminder->invoice->isPaid()) {
                $reminder->update(['status' => 'cancelled']);
                continue;
            }

            SendReminderJob::dispatch($reminder);
            $count++;
        }

        $this->info("Dispatched {$count} reminder jobs");

        return 0;
    }
}