<? php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internet_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('router_id')->nullable()->constrained('routers')->onDelete('set null');
            $table->string('profile', 100)->nullable();
            $table->string('rate_limit', 50)->nullable(); // e.g., "5M/5M"
            $table->string('limit_at', 50)->nullable(); // e.g., "3M/3M"
            $table->string('priority', 20)->nullable(); // e.g., "8/8"
            $table->integer('auto_soft_limit')->default(7); // days after due date
            $table->integer('auto_suspend')->default(14); // days after due date
            $table->timestamps();

            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internet_services');
    }
};