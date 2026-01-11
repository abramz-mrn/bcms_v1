<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('location', 200)->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address', 45);
            $table->integer('api_port')->default(8728);
            $table->integer('ssh_port')->default(22);
            $table->string('api_username', 100);
            $table->text('api_password'); // encrypted
            $table->text('api_certificate')->nullable();
            $table->boolean('tls_enabled')->default(true);
            $table->boolean('ssh_enabled')->default(false);
            $table->enum('status', ['online', 'offline', 'maintenance'])->default('offline');
            $table->integer('sync_interval')->default(300);
            $table->timestamp('last_sync_at')->nullable();
            $table->json('config_backup')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routers');
    }
};