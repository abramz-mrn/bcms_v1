<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->date('registration_date');
            $table->boolean('email_consent')->default(true);
            $table->boolean('sms_consent')->default(true);
            $table->boolean('whatsapp_consent')->default(true);
            $table->string('document_sf', 255)->nullable(); // Service Form
            $table->string('document_asf', 255)->nullable(); // Activation Service Form
            $table->string('document_pks', 255)->nullable(); // Perjanjian Kerja Sama
            $table->enum('status', ['Registered', 'Active', 'Soft-Limit', 'Suspend', 'Terminated'])->default('Registered');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('customer_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};