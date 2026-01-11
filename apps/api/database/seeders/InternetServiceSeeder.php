<? php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InternetServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'id' => 1,
                'product_id' => 1, // Paket Basic
                'router_id' => 1,
                'profile' => 'PROFILE-BASIC-10M',
                'rate_limit' => '10M/5M',
                'limit_at' => '8M/4M',
                'priority' => '8/8',
                'auto_soft_limit' => 7,
                'auto_suspend' => 14,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'product_id' => 2, // Paket Medium
                'router_id' => 1,
                'profile' => 'PROFILE-MEDIUM-20M',
                'rate_limit' => '20M/10M',
                'limit_at' => '15M/8M',
                'priority' => '7/7',
                'auto_soft_limit' => 7,
                'auto_suspend' => 14,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'product_id' => 3, // Paket Premium
                'router_id' => 1,
                'profile' => 'PROFILE-PREMIUM-50M',
                'rate_limit' => '50M/25M',
                'limit_at' => '40M/20M',
                'priority' => '6/6',
                'auto_soft_limit' => 7,
                'auto_suspend' => 14,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'product_id' => 4, // SOHO-20
                'router_id' => 1,
                'profile' => 'PROFILE-SOHO-20M',
                'rate_limit' => '20M/20M',
                'limit_at' => '15M/15M',
                'priority' => '5/5',
                'auto_soft_limit' => 5,
                'auto_suspend' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'product_id' => 5, // SOHO-50
                'router_id' => 1,
                'profile' => 'PROFILE-SOHO-50M',
                'rate_limit' => '50M/50M',
                'limit_at' => '40M/40M',
                'priority' => '4/4',
                'auto_soft_limit' => 5,
                'auto_suspend' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'product_id' => 6, // SOHO-100
                'router_id' => 2,
                'profile' => 'PROFILE-CORP-100M',
                'rate_limit' => '100M/100M',
                'limit_at' => '80M/80M',
                'priority' => '3/3',
                'auto_soft_limit' => 3,
                'auto_suspend' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('internet_services')->insert($services);
    }
}