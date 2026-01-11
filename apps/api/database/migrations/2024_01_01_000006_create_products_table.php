<? php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema:: create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->enum('type', ['Internet Services', 'Additional Services', 'Other Services', 'Equipments']);
            $table->text('description')->nullable();
            $table->enum('market_segment', ['Residensial', 'SOHO/UMKM', 'Corporate', 'Others']);
            $table->enum('billing_cycle', ['One time charge', 'Weekly', 'Monthly', 'Quarterly', 'Semi-annually', 'Annually']);
            $table->decimal('price', 15, 2);
            $table->decimal('tax_rate', 5, 2)->default(11. 00);
            $table->boolean('tax_included')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('type');
            $table->index('market_segment');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};