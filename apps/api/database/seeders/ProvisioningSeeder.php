<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class ProvisioningSeeder extends Seeder
{
    public function run(): void
    {
        $provisionings = [
            [
                'id' => 1,
                'subscription_id' => 1,
                'router_id' => 1,
                'device_brand' => 'Huawei',
                'device_type' => 'ONT HG8245H5',
                'device_sn' => 'HW123456789001',
                'device_mac' => 'AA:BB:CC:DD:EE:01',
                'device_conn' => 'PPPoE',
                'pppoe_name' => 'pppoe-cust0001',
                'pppoe_password' => Crypt::encryptString('pppoe123456'),
                'static_ip' => null,
                'static_gateway' => null,
                'activation_date' => now()->subMonths(6)->toDateString(),
                'technician_name' => 'Yogi',
                'technician_notes' => 'Instalasi lancar',
                'created_by' => 4,
                'created_at' => now()->subMonths(6),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'subscription_id' => 2,
                'router_id' => 1,
                'device_brand' => 'ZTE',
                'device_type' => 'ONT F660',
                'device_sn' => 'ZTE123456789002',
                'device_mac' => 'AA:BB:CC:DD:EE:02',
                'device_conn' => 'PPPoE',
                'pppoe_name' => 'pppoe-cust0002',
                'pppoe_password' => Crypt::encryptString('pppoe234567'),
                'static_ip' => null,
                'static_gateway' => null,
                'activation_date' => now()->subMonths(5)->toDateString(),
                'technician_name' => 'Yogi',
                'technician_notes' => 'Instalasi lancar, kabel 50m',
                'created_by' => 4,
                'created_at' => now()->subMonths(5),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'subscription_id' => 3,
                'router_id' => 1,
                'device_brand' => 'Huawei',
                'device_type' => 'ONT HG8245H5',
                'device_sn' => 'HW123456789003',
                'device_mac' => 'AA:BB:CC:DD:EE:03',
                'device_conn' => 'PPPoE',
                'pppoe_name' => 'pppoe-cust0003',
                'pppoe_password' => Crypt::encryptString('pppoe345678'),
                'static_ip' => null,
                'static_gateway' => null,
                'activation_date' => now()->subMonths(4)->toDateString(),
                'technician_name' => 'Yogi',
                'technician_notes' => 'Pelanggan request tambahan AP',
                'created_by' => 4,
                'created_at' => now()->subMonths(4),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'subscription_id' => 4,
                'router_id' => 2,
                'device_brand' => 'Mikrotik',
                'device_type' => 'RB750Gr3',
                'device_sn' => 'MT123456789004',
                'device_mac' => 'AA: BB:CC:DD:EE:04',
                'device_conn' => 'Static-IP',
                'pppoe_name' => null,
                'pppoe_password' => null,
                'static_ip' => '103.123.45.100',
                'static_gateway' => '103.123.45.1',
                'activation_date' => now()->subMonths(3)->toDateString(),
                'technician_name' => 'Yogi',
                'technician_notes' => 'Corporate customer, static IP',
                'created_by' => 4,
                'created_at' => now()->subMonths(3),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'subscription_id' => 5,
                'router_id' => 1,
                'device_brand' => 'Huawei',
                'device_type' => 'ONT HG8245H5',
                'device_sn' => 'HW123456789005',
                'device_mac' => 'AA:BB:CC:DD:EE:05',
                'device_conn' => 'PPPoE',
                'pppoe_name' => 'pppoe-cust0005',
                'pppoe_password' => Crypt::encryptString('pppoe567890'),
                'static_ip' => null,
                'static_gateway' => null,
                'activation_date' => now()->subMonths(2)->toDateString(),
                'technician_name' => 'Yogi',
                'technician_notes' => 'Instalasi normal',
                'created_by' => 4,
                'created_at' => now()->subMonths(2),
                'updated_at' => now(),
            ],
        ];

        DB::table('provisionings')->insert($provisionings);
    }
}