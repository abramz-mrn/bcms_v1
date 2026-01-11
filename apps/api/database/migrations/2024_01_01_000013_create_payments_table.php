<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema:: create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('restrict');
            $table->enum('payment_method', ['cash', 'transfer', 'virtual_account']);
            $table->enum('payment_gateway', ['Midtrans', 'Xendit', 'Manual'])->nullable();
            $table->string('transaction_id', 100)->nullable();
            $table->decimal('amount', 15, 2);
            $table->decimal('fee', 15, 2)->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->string('reference_number', 100)->nullable();
            $table->string('document_proof', 255)->nullable();
            $table->enum('status', ['pending', 'verified', 'rejected', 'refunded'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('invoice_id');
            $table->index('transaction_id');
            $table->index('payment_gateway');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};