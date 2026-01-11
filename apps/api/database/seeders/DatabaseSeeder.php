<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CompanySeeder::class,
            UserGroupSeeder::class,
            UserSeeder::class,
            BrandSeeder::class,
            RouterSeeder::class,
            ProductSeeder::class,
            InternetServiceSeeder::class,
            PromotionSeeder::class,
            CustomerSeeder::class,
            SubscriptionSeeder::class,
            ProvisioningSeeder:: class,
            InvoiceSeeder::class,
            PaymentSeeder::class,
            TemplateSeeder::class,
            TicketSeeder::class,
        ]);
    }
}