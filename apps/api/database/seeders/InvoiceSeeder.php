<? php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();
        
        $invoices = [
            // Customer 1 - Paid last month, Unpaid current month
            [
                'id' => 1,
                'invoice_no' => 'INV-2025-12-0001',
                'customer_id' => 1,
                'subscription_id' => 1,
                'product_id' => 2,
                'period_start' => $lastMonth->copy()->startOfMonth()->toDateString(),
                'period_end' => $lastMonth->copy()->endOfMonth()->toDateString(),
                'amount' => 225225.00,
                'tax_amount' => 24775.00,
                'discount_amount' => 0,
                'total_amount' => 250000.00,
                'due_date' => $lastMonth->copy()->day(20)->toDateString(),
                'status' => 'Paid',
                'created_by' => 3,
                'created_at' => $lastMonth->copy()->startOfMonth()->subDays(7),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'invoice_no' => 'INV-2026-01-0001',
                'customer_id' => 1,
                'subscription_id' => 1,
                'product_id' => 2,
                'period_start' => $currentMonth->copy()->startOfMonth()->toDateString(),
                'period_end' => $currentMonth->copy()->endOfMonth()->toDateString(),
                'amount' => 225225.00,
                'tax_amount' => 24775.00,
                'discount_amount' => 0,
                'total_amount' => 250000.00,
                'due_date' => $currentMonth->copy()->day(20)->toDateString(),
                'status' => 'Unpaid',
                'created_by' => 3,
                'created_at' => $currentMonth->copy()->startOfMonth()->subDays(7),
                'updated_at' => now(),
            ],
            // Customer 2 - Paid
            [
                'id' => 3,
                'invoice_no' => 'INV-2026-01-0002',
                'customer_id' => 2,
                'subscription_id' => 2,
                'product_id' => 1,
                'period_start' => $currentMonth->copy()->startOfMonth()->toDateString(),
                'period_end' => $currentMonth->copy()->endOfMonth()->toDateString(),
                'amount' => 135135.00,
                'tax_amount' => 14865.00,
                'discount_amount' => 0,
                'total_amount' => 150000.00,
                'due_date' => $currentMonth->copy()->day(20)->toDateString(),
                'status' => 'Paid',
                'created_by' => 3,
                'created_at' => $currentMonth->copy()->startOfMonth()->subDays(7),
                'updated_at' => now(),
            ],
            // Customer 3 - Unpaid
            [
                'id' => 4,
                'invoice_no' => 'INV-2026-01-0003',
                'customer_id' => 3,
                'subscription_id' => 3,
                'product_id' => 3,
                'period_start' => $currentMonth->copy()->startOfMonth()->toDateString(),
                'period_end' => $currentMonth->copy()->endOfMonth()->toDateString(),
                'amount' => 405405.00,
                'tax_amount' => 44595.00,
                'discount_amount' => 0,
                'total_amount' => 450000.00,
                'due_date' => $currentMonth->copy()->day(20)->toDateString(),
                'status' => 'Unpaid',
                'created_by' => 3,
                'created_at' => $currentMonth->copy()->startOfMonth()->subDays(7),
                'updated_at' => now(),
            ],
            // Customer 4 (Corporate) - Paid
            [
                'id' => 5,
                'invoice_no' => 'INV-2026-01-0004',
                'customer_id' => 4,
                'subscription_id' => 4,
                'product_id' => 6,
                'period_start' => $currentMonth->copy()->startOfMonth()->toDateString(),
                'period_end' => $currentMonth->copy()->endOfMonth()->toDateString(),
                'amount' => 1351351.00,
                'tax_amount' => 148649.00,
                'discount_amount' => 0,
                'total_amount' => 1500000.00,
                'due_date' => $currentMonth->copy()->day(20)->toDateString(),
                'status' => 'Paid',
                'created_by' => 3,
                'created_at' => $currentMonth->copy()->startOfMonth()->subDays(7),
                'updated_at' => now(),
            ],
            // Customer 5 - Suspend (Unpaid overdue)
            [
                'id' => 6,
                'invoice_no' => 'INV-2025-12-0005',
                'customer_id' => 5,
                'subscription_id' => 5,
                'product_id' => 2,
                'period_start' => $lastMonth->copy()->startOfMonth()->toDateString(),
                'period_end' => $lastMonth->copy()->endOfMonth()->toDateString(),
                'amount' => 225225.00,
                'tax_amount' => 24775.00,
                'discount_amount' => 0,
                'total_amount' => 250000.00,
                'due_date' => $lastMonth->copy()->day(20)->toDateString(),
                'status' => 'Unpaid',
                'notes' => 'Customer suspended - overdue payment',
                'created_by' => 3,
                'created_at' => $lastMonth->copy()->startOfMonth()->subDays(7),
                'updated_at' => now(),
            ],
        ];

        DB::table('invoices')->insert($invoices);
    }
}