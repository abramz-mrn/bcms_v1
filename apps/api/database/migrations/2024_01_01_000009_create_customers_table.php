<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema:: create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 200);
            $table->string('id_card_number', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('pos', 10)->nullable();
            $table->string('group_area', 100)->nullable();
            $table->string('phone', 20);
            $table->string('email', 100)->nullable();
            $table->string('document_id_card', 255)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index('code');
            $table->index('phone');
            $table->index('email');
            $table->index('group_area');
        });
    }

    public function down(): void
    {
        Schema:: dropIfExists('customers');
    }
};