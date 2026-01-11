<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'id' => 1,
                'code' => 'CUST-2024-0001',
                'name' => 'Ahmad Sudirman',
                'id_card_number' => '3216011234567801',
                'address' => 'Jl. Metland Residence Blok A1 No.  10',
                'city' => 'Kab. Bekasi',
                'state' => 'Jawa Barat',
                'pos' => '17530',
                'group_area' => 'Metland Cibitung',
                'phone' => '081311111111',
                'email' => 'ahmad.sudirman@gmail.com',
                'notes' => 'Pelanggan prioritas',
                'created_by' => 1,
                'created_at' => now()->subMonths(6),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'code' => 'CUST-2024-0002',
                'name' => 'Budi Santoso',
                'id_card_number' => '3216011234567802',
                'address' => 'Jl. Metland Residence Blok B2 No. 15',
                'city' => 'Kab. Bekasi',
                'state' => 'Jawa Barat',
                'pos' => '17530',
                'group_area' => 'Metland Cibitung',
                'phone' => '081322222222',
                'email' => 'budi.santoso@gmail.com',
                'notes' => null,
                'created_by' => 1,
                'created_at' => now()->subMonths(5),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'code' => 'CUST-2024-0003',
                'name' => 'Citra Dewi',
                'id_card_number' => '3216011234567803',
                'address' => 'Jl.  Metland Residence Blok C3 No. 20',
                'city' => 'Kab. Bekasi',
                'state' => 'Jawa Barat',
                'pos' => '17530',
                'group_area' => 'Metland Cibitung',
                'phone' => '081333333333',
                'email' => 'citra.dewi@gmail.com',
                'notes' => null,
                'created_by' => 2,
                'created_at' => now()->subMonths(4),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'code' => 'CUST-2024-0004',
                'name' => 'PT. Maju Bersama',
                'id_card_number' => null,
                'address' => 'Ruko Lippo Cikarang Blok D1 No. 5',
                'city' => 'Kab. Bekasi',
                'state' => 'Jawa Barat',
                'pos' => '17550',
                'group_area' => 'Lippo Cikarang',
                'phone' => '081344444444',
                'email' => 'admin@majubersama.co.id',
                'notes' => 'Pelanggan Corporate',
                'created_by' => 1,
                'created_at' => now()->subMonths(3),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'code' => 'CUST-2024-0005',
                'name' => 'Eko Prasetyo',
                'id_card_number' => '3216011234567805',
                'address' => 'Jl. Metland Residence Blok E5 No.  8',
                'city' => 'Kab. Bekasi',
                'state' => 'Jawa Barat',
                'pos' => '17530',
                'group_area' => 'Metland Cibitung',
                'phone' => '081355555555',
                'email' => 'eko.prasetyo@gmail.com',
                'notes' => 'Suspend karena belum bayar',
                'created_by' => 2,
                'created_at' => now()->subMonths(2),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'code' => 'CUST-2024-0006',
                'name' => 'Fitri Handayani',
                'id_card_number' => '3216011234567806',
                'address' => 'Jl. Metland Residence Blok F6 No. 12',
                'city' => 'Kab. Bekasi',
                'state' => 'Jawa Barat',
                'pos' => '17530',
                'group_area' => 'Metland Cibitung',
                'phone' => '081366666666',
                'email' => 'fitri.h@gmail.com',
                'notes' => null,
                'created_by' => 1,
                'created_at' => now()->subMonth(),
                'updated_at' => now(),
            ],
        ];

        DB::table('customers')->insert($customers);
    }
}