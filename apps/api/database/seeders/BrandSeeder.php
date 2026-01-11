<? php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('brands')->insert([
            'id' => 1,
            'company_id' => 1,
            'name' => 'Maroon-NET',
            'description' => 'Brand layanan internet PT.  Trira Inti Utama untuk wilayah Bekasi dan sekitarnya.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}