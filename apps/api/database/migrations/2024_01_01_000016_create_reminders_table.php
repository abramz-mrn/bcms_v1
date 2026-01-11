<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('template_id')->constrained('templates')->onDelete('restrict');
            $table->enum('channel', ['email', 'sms', 'whatsapp']);
            $table->enum('trigger_type', ['before_due', 'on_due', 'after_due', 'pre_soft_limit', 'pre_suspend']);
            $table->integer('days_offset')->default(0); // negative for before, positive for after
            $table->timestamp('scheduled_at');
            $table->timestamp('sent_at')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed', 'cancelled'])->default('pending');
            $table->text('error_message')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('status');
            $table->index('scheduled_at');
            $table->index('invoice_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};