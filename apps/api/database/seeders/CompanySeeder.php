<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('companies')->insert([
            'id' => 1,
            'name' => 'PT. Trira Inti Utama',
            'alias' => 'TIU',
            'address' => 'Ruko Kemanggisan Blok O4 No.  6 Metland Cibitung',
            'city' => 'Kab. Bekasi',
            'state' => 'Jawa Barat',
            'pos' => '17530',
            'phone' => '021-89950888',
            'email' => 'info@maroon-net.id',
            'logo' => null,
            'bank_account' => json_encode([
                'bank_name' => 'Mandiri',
                'account_number' => '156-00-2388849-0',
                'account_name' => 'PT. Trira Inti Utama'
            ]),
            'npwp' => '50.520.877.7-413.000',
            'updated_at' => now(),
        ]);
    }
}