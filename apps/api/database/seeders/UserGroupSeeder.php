<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            [
                'id' => 1,
                'name' => 'Administrator',
                'permissions' => json_encode([
                    'dashboard' => ['view'],
                    'users' => ['view', 'create', 'update', 'delete'],
                    'user_groups' => ['view', 'create', 'update', 'delete'],
                    'companies' => ['view', 'update'],
                    'brands' => ['view', 'create', 'update', 'delete'],
                    'products' => ['view', 'create', 'update', 'delete'],
                    'internet_services' => ['view', 'create', 'update', 'delete'],
                    'promotions' => ['view', 'create', 'update', 'delete'],
                    'routers' => ['view', 'create', 'update', 'delete', 'sync', 'test'],
                    'customers' => ['view', 'create', 'update', 'delete'],
                    'subscriptions' => ['view', 'create', 'update', 'delete'],
                    'provisionings' => ['view', 'create', 'update', 'delete', 'provision', 'ping'],
                    'invoices' => ['view', 'create', 'update', 'delete', 'generate'],
                    'payments' => ['view', 'create', 'verify', 'reject'],
                    'tickets' => ['view', 'create', 'update', 'assign', 'resolve', 'close'],
                    'templates' => ['view', 'create', 'update', 'delete'],
                    'reminders' => ['view', 'create', 'send'],
                    'reports' => ['view', 'export'],
                    'audit_logs' => ['view'],
                    'settings' => ['view', 'update'],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Supervisor',
                'permissions' => json_encode([
                    'dashboard' => ['view'],
                    'users' => ['view', 'create', 'update'],
                    'user_groups' => ['view'],
                    'companies' => ['view'],
                    'brands' => ['view', 'create', 'update', 'delete'],
                    'products' => ['view', 'create', 'update', 'delete'],
                    'internet_services' => ['view', 'create', 'update', 'delete'],
                    'promotions' => ['view', 'create', 'update', 'delete'],
                    'routers' => ['view', 'create', 'update', 'delete', 'sync', 'test'],
                    'customers' => ['view', 'create', 'update'],
                    'subscriptions' => ['view', 'create', 'update'],
                    'provisionings' => ['view', 'create', 'update', 'provision', 'ping'],
                    'invoices' => ['view', 'create', 'update', 'generate'],
                    'payments' => ['view', 'verify'],
                    'tickets' => ['view', 'create', 'update', 'assign', 'resolve', 'close'],
                    'templates' => ['view', 'create', 'update'],
                    'reminders' => ['view', 'send'],
                    'reports' => ['view', 'export'],
                    'audit_logs' => ['view'],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Finance',
                'permissions' => json_encode([
                    'dashboard' => ['view'],
                    'customers' => ['view'],
                    'subscriptions' => ['view'],
                    'invoices' => ['view', 'create', 'update', 'generate'],
                    'payments' => ['view', 'create', 'verify', 'reject'],
                    'tickets' => ['view'],
                    'reminders' => ['view', 'send'],
                    'reports' => ['view', 'export'],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Support',
                'permissions' => json_encode([
                    'dashboard' => ['view'],
                    'customers' => ['view'],
                    'subscriptions' => ['view'],
                    'provisionings' => ['view'],
                    'invoices' => ['view'],
                    'tickets' => ['view', 'create', 'update', 'resolve'],
                    'templates' => ['view'],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'NOC/Technician',
                'permissions' => json_encode([
                    'dashboard' => ['view'],
                    'routers' => ['view', 'sync', 'test'],
                    'customers' => ['view'],
                    'subscriptions' => ['view', 'update'],
                    'provisionings' => ['view', 'create', 'update', 'provision', 'ping'],
                    'tickets' => ['view', 'update', 'resolve'],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('user_groups')->insert($groups);
    }
}