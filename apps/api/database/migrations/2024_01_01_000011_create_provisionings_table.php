<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provisionings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('subscriptions')->onDelete('cascade');
            $table->foreignId('router_id')->constrained('routers')->onDelete('restrict');
            $table->string('device_brand', 100)->nullable();
            $table->string('device_type', 100)->nullable();
            $table->string('device_sn', 100)->nullable();
            $table->string('device_mac', 17)->nullable();
            $table->enum('device_conn', ['PPPoE', 'Static-IP']);
            $table->string('pppoe_name', 100)->nullable();
            $table->text('pppoe_password')->nullable(); // encrypted
            $table->string('static_ip', 45)->nullable();
            $table->string('static_gateway', 45)->nullable();
            $table->date('activation_date')->nullable();
            $table->string('technician_name', 100)->nullable();
            $table->string('document_speedtest', 255)->nullable();
            $table->text('technician_notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('subscription_id');
            $table->index('router_id');
            $table->index('device_conn');
            $table->index('pppoe_name');
            $table->index('static_ip');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provisionings');
    }
};