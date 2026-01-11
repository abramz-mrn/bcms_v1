<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $subscriptions = [
            [
                'id' => 1,
                'customer_id' => 1,
                'product_id' => 2, // Paket Medium
                'registration_date' => now()->subMonths(6)->toDateString(),
                'email_consent' => true,
                'sms_consent' => true,
                'whatsapp_consent' => true,
                'status' => 'Active',
                'created_by' => 1,
                'created_at' => now()->subMonths(6),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'customer_id' => 2,
                'product_id' => 1, // Paket Basic
                'registration_date' => now()->subMonths(5)->toDateString(),
                'email_consent' => true,
                'sms_consent' => false,
                'whatsapp_consent' => true,
                'status' => 'Active',
                'created_by' => 1,
                'created_at' => now()->subMonths(5),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'customer_id' => 3,
                'product_id' => 3, // Paket Premium
                'registration_date' => now()->subMonths(4)->toDateString(),
                'email_consent' => true,
                'sms_consent' => true,
                'whatsapp_consent' => true,
                'status' => 'Active',
                'created_by' => 2,
                'created_at' => now()->subMonths(4),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'customer_id' => 4,
                'product_id' => 6, // SOHO-100
                'registration_date' => now()->subMonths(3)->toDateString(),
                'email_consent' => true,
                'sms_consent' => true,
                'whatsapp_consent' => true,
                'status' => 'Active',
                'created_by' => 1,
                'created_at' => now()->subMonths(3),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'customer_id' => 5,
                'product_id' => 2, // Paket Medium
                'registration_date' => now()->subMonths(2)->toDateString(),
                'email_consent' => true,
                'sms_consent' => true,
                'whatsapp_consent' => true,
                'status' => 'Suspend',
                'created_by' => 2,
                'created_at' => now()->subMonths(2),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'customer_id' => 6,
                'product_id' => 1, // Paket Basic
                'registration_date' => now()->subMonth()->toDateString(),
                'email_consent' => true,
                'sms_consent' => false,
                'whatsapp_consent' => true,
                'status' => 'Registered',
                'created_by' => 1,
                'created_at' => now()->subMonth(),
                'updated_at' => now(),
            ],
        ];

        DB::table('subscriptions')->insert($subscriptions);
    }
}