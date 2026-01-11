<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        $promotions = [
            [
                'id' => 1,
                'product_id' => 1,
                'name' => 'Promo Tahun Baru 2026',
                'description' => 'Diskon 20% untuk Paket Basic selama bulan Januari 2026',
                'start_date' => '2026-01-01',
                'end_date' => '2026-01-31',
                'discount' => 30000.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'product_id' => 2,
                'name' => 'Promo Tahun Baru 2026',
                'description' => 'Diskon 20% untuk Paket Medium selama bulan Januari 2026',
                'start_date' => '2026-01-01',
                'end_date' => '2026-01-31',
                'discount' => 50000.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('promotions')->insert($promotions);
    }
}