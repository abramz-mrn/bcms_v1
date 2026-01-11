<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $lastMonth = Carbon::now()->subMonth();
        $currentMonth = Carbon::now();

        $payments = [
            // Payment for Invoice 1 (Customer 1 - Last month)
            [
                'id' => 1,
                'invoice_id' => 1,
                'payment_method' => 'virtual_account',
                'payment_gateway' => 'Xendit',
                'transaction_id' => 'XND-' . strtoupper(uniqid()),
                'amount' => 250000.00,
                'fee' => 4000.00,
                'paid_at' => $lastMonth->copy()->day(15)->setHour(10)->setMinute(30),
                'reference_number' => 'REF-2025-12-0001',
                'status' => 'verified',
                'notes' => 'Payment via BCA Virtual Account',
                'created_by' => null,
                'verified_by' => 3,
                'verified_at' => $lastMonth->copy()->day(15)->setHour(10)->setMinute(31),
                'created_at' => $lastMonth->copy()->day(15)->setHour(10)->setMinute(30),
                'updated_at' => now(),
            ],
            // Payment for Invoice 3 (Customer 2 - Current month)
            [
                'id' => 2,
                'invoice_id' => 3,
                'payment_method' => 'transfer',
                'payment_gateway' => 'Manual',
                'transaction_id' => null,
                'amount' => 150000.00,
                'fee' => 0,
                'paid_at' => $currentMonth->copy()->day(5)->setHour(14)->setMinute(20),
                'reference_number' => 'TRF-2026-01-0001',
                'document_proof' => 'payments/proof_2026010001.jpg',
                'status' => 'verified',
                'notes' => 'Transfer via Mandiri Mobile Banking',
                'created_by' => 3,
                'verified_by' => 3,
                'verified_at' => $currentMonth->copy()->day(5)->setHour(15)->setMinute(0),
                'created_at' => $currentMonth->copy()->day(5)->setHour(14)->setMinute(20),
                'updated_at' => now(),
            ],
            // Payment for Invoice 5 (Customer 4 - Corporate)
            [
                'id' => 3,
                'invoice_id' => 5,
                'payment_method' => 'transfer',
                'payment_gateway' => 'Manual',
                'transaction_id' => null,
                'amount' => 1500000.00,
                'fee' => 0,
                'paid_at' => $currentMonth->copy()->day(3)->setHour(9)->setMinute(15),
                'reference_number' => 'TRF-2026-01-0002',
                'document_proof' => 'payments/proof_2026010002.jpg',
                'status' => 'verified',
                'notes' => 'Corporate payment via Bank Transfer',
                'created_by' => 3,
                'verified_by' => 3,
                'verified_at' => $currentMonth->copy()->day(3)->setHour(10)->setMinute(0),
                'created_at' => $currentMonth->copy()->day(3)->setHour(9)->setMinute(15),
                'updated_at' => now(),
            ],
            // Pending payment for Invoice 4 (Customer 3)
            [
                'id' => 4,
                'invoice_id' => 4,
                'payment_method' => 'transfer',
                'payment_gateway' => 'Manual',
                'transaction_id' => null,
                'amount' => 450000.00,
                'fee' => 0,
                'paid_at' => null,
                'reference_number' => 'TRF-2026-01-0003',
                'document_proof' => 'payments/proof_2026010003.jpg',
                'status' => 'pending',
                'notes' => 'Menunggu verifikasi bukti transfer',
                'created_by' => 3,
                'verified_by' => null,
                'verified_at' => null,
                'created_at' => $currentMonth->copy()->day(8)->setHour(16)->setMinute(45),
                'updated_at' => now(),
            ],
        ];

        DB::table('payments')->insert($payments);
    }
}