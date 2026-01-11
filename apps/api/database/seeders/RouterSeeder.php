<? php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class RouterSeeder extends Seeder
{
    public function run(): void
    {
        $routers = [
            [
                'id' => 1,
                'name' => 'Router-POP-Cibitung-01',
                'location' => 'POP Cibitung - Ruko Metland',
                'description' => 'Router distribusi utama wilayah Cibitung',
                'ip_address' => '192.168.1.1',
                'api_port' => 8729,
                'ssh_port' => 22,
                'api_username' => 'api_user',
                'api_password' => Crypt::encryptString('api_password_secure'),
                'tls_enabled' => true,
                'ssh_enabled' => true,
                'status' => 'offline',
                'sync_interval' => 300,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Router-POP-Cikarang-01',
                'location' => 'POP Cikarang - Lippo Cikarang',
                'description' => 'Router distribusi utama wilayah Cikarang',
                'ip_address' => '192.168.2.1',
                'api_port' => 8729,
                'ssh_port' => 22,
                'api_username' => 'api_user',
                'api_password' => Crypt::encryptString('api_password_secure'),
                'tls_enabled' => true,
                'ssh_enabled' => true,
                'status' => 'offline',
                'sync_interval' => 300,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('routers')->insert($routers);
    }
}