<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema:: create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 50)->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions')->onDelete('set null');
            $table->string('caller_name', 100);
            $table->string('phone', 20);
            $table->string('email', 100)->nullable();
            $table->enum('category', ['information', 'technical', 'billing', 'complaint']);
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->string('subject', 200);
            $table->text('description');
            $table->enum('status', ['open', 'assigned', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('sla_due_date')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->integer('customer_rating')->nullable(); // 1-5
            $table->text('customer_feedback')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('ticket_number');
            $table->index('status');
            $table->index('category');
            $table->index('priority');
            $table->index('customer_id');
            $table->index('assigned_to');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};