<?php

namespace Database\Seeders;

use App\Modules\Accounts\Models\Account;
use App\Modules\Transfers\Enums\TransferStatus;
use App\Modules\Transfers\Enums\TransferType;
use App\Modules\Transfers\Models\Transfer;
use Illuminate\Database\Seeder;

class TransferSeeder extends Seeder
{
    public function run(): void
    {
        $acc1 = Account::where('account_number', '1000100001')->first();
        $acc2 = Account::where('account_number', '1000200001')->first();
        $acc3 = Account::where('account_number', '1000300001')->first();
        $accUSD = Account::where('account_number', '1000100003')->first();

        $transfers = [
            [
                'transfer_reference' => 'TRF-2026-000001',
                'from_account_id' => $acc1?->id,
                'to_account_id' => $acc2?->id,
                'customer_id' => $acc1?->customer_id,
                'transfer_type' => TransferType::Internal,
                'status' => TransferStatus::Completed,
                'amount' => 500000.00,
                'currency' => 'SYP',
                'fees' => 1000.00,
                'total_deducted' => 501000.00,
                'recipient_name' => 'Layla Mansour',
                'recipient_account' => '1000200001',
                'recipient_bank' => 'Bank System',
                'description' => 'تحويل عائلي - Family Support Transfer',
                'completed_at' => now()->subDays(5),
            ],
            [
                'transfer_reference' => 'TRF-2026-000002',
                'from_account_id' => $acc3?->id,
                'to_account_id' => $acc1?->id,
                'customer_id' => $acc3?->customer_id,
                'transfer_type' => TransferType::Internal,
                'status' => TransferStatus::Completed,
                'amount' => 2500000.00,
                'currency' => 'SYP',
                'fees' => 2500.00,
                'total_deducted' => 2502500.00,
                'recipient_name' => 'Ahmad Al-Sayed',
                'recipient_account' => '1000100001',
                'recipient_bank' => 'Bank System',
                'description' => 'تسوية أتعاب استشارية - Consulting Services Settlement',
                'completed_at' => now()->subDays(10),
            ],
            [
                'transfer_reference' => 'TRF-2026-000003',
                'from_account_id' => $accUSD?->id,
                'to_account_id' => null,
                'customer_id' => $accUSD?->customer_id,
                'transfer_type' => TransferType::International,
                'status' => TransferStatus::Completed,
                'amount' => 3200.00,
                'currency' => 'USD',
                'fees' => 35.00,
                'total_deducted' => 3235.00,
                'recipient_name' => 'Global Logistics FZE',
                'recipient_account' => 'AE280330000001234567890',
                'recipient_bank' => 'Emirates NBD',
                'recipient_bank_address' => 'Baniyas Road, Deira, Dubai, UAE',
                'swift_code' => 'EBILAEADXXX',
                'iban' => 'AE280330000001234567890',
                'description' => 'سداد فاتورة شحن بضائع - International Freight Invoice Settlement',
                'completed_at' => now()->subDays(15),
            ],
            [
                'transfer_reference' => 'TRF-2026-000004',
                'from_account_id' => $acc1?->id,
                'to_account_id' => null,
                'customer_id' => $acc1?->customer_id,
                'transfer_type' => TransferType::External,
                'status' => TransferStatus::Pending,
                'amount' => 750000.00,
                'currency' => 'SYP',
                'fees' => 2000.00,
                'total_deducted' => 752000.00,
                'recipient_name' => 'Samer Al-Khatib',
                'recipient_account' => 'SY1100223344556677889900',
                'recipient_bank' => 'Commercial Bank of Syria',
                'description' => 'دفعة مورد - Supplier Payment Order',
                'scheduled_for' => now()->addDay(),
            ],
        ];

        foreach ($transfers as $tr) {
            Transfer::updateOrCreate(
                ['transfer_reference' => $tr['transfer_reference']],
                $tr
            );
        }
    }
}
