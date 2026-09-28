<?php

namespace Database\Seeders;

use App\Modules\Accounts\Models\Account;
use App\Modules\Cards\Enums\CardBrand;
use App\Modules\Cards\Enums\CardStatus;
use App\Modules\Cards\Enums\CardType;
use App\Modules\Cards\Models\Card;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CardSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = Account::with('customer')->get()->keyBy('account_number');

        $cardsData = [
            [
                'account_number' => '1000100001',
                'card_number' => '4200112233440001',
                'card_holder_name' => 'AHMAD AL-SAYED',
                'card_type' => CardType::Debit,
                'card_brand' => CardBrand::Visa,
                'expiry_month' => 9,
                'expiry_year' => 2028,
                'cvv' => '321',
                'pin' => Hash::make('1234'),
                'status' => CardStatus::Active,
                'daily_limit' => 2000000.00,
                'monthly_limit' => 20000000.00,
                'international_enabled' => false,
                'online_enabled' => true,
                'contactless_enabled' => true,
                'issued_at' => now()->subYears(2),
                'expires_at' => now()->addYears(2),
                'activated_at' => now()->subYears(2),
            ],
            [
                'account_number' => '1000100003',
                'card_number' => '5400112233440002',
                'card_holder_name' => 'AHMAD AL-SAYED',
                'card_type' => CardType::Credit,
                'card_brand' => CardBrand::Mastercard,
                'expiry_month' => 6,
                'expiry_year' => 2027,
                'cvv' => '784',
                'pin' => Hash::make('1234'),
                'status' => CardStatus::Active,
                'daily_limit' => 5000.00,
                'monthly_limit' => 30000.00,
                'international_enabled' => true,
                'online_enabled' => true,
                'contactless_enabled' => true,
                'issued_at' => now()->subMonths(6),
                'expires_at' => now()->addYears(3),
                'activated_at' => now()->subMonths(6),
            ],
            [
                'account_number' => '1000200001',
                'card_number' => '4200112233440003',
                'card_holder_name' => 'LAYLA MANSOUR',
                'card_type' => CardType::Debit,
                'card_brand' => CardBrand::Visa,
                'expiry_month' => 11,
                'expiry_year' => 2028,
                'cvv' => '512',
                'pin' => Hash::make('1234'),
                'status' => CardStatus::Active,
                'daily_limit' => 1500000.00,
                'monthly_limit' => 15000000.00,
                'international_enabled' => false,
                'online_enabled' => true,
                'contactless_enabled' => true,
                'issued_at' => now()->subMonths(8),
                'expires_at' => now()->addYears(2),
                'activated_at' => now()->subMonths(8),
            ],
            [
                'account_number' => '1000300002',
                'card_number' => '5400112233440004',
                'card_holder_name' => 'OMAR KHALED',
                'card_type' => CardType::Debit,
                'card_brand' => CardBrand::Mastercard,
                'expiry_month' => 4,
                'expiry_year' => 2029,
                'cvv' => '941',
                'pin' => Hash::make('1234'),
                'status' => CardStatus::Active,
                'daily_limit' => 10000000.00,
                'monthly_limit' => 100000000.00,
                'international_enabled' => true,
                'online_enabled' => true,
                'contactless_enabled' => true,
                'issued_at' => now()->subYears(2),
                'expires_at' => now()->addYears(3),
                'activated_at' => now()->subYears(2),
            ],
            [
                'account_number' => '1000400001',
                'card_number' => '4200112233440005',
                'card_holder_name' => 'NOUR AL-HASSAN',
                'card_type' => CardType::Debit,
                'card_brand' => CardBrand::Visa,
                'expiry_month' => 8,
                'expiry_year' => 2028,
                'cvv' => '629',
                'pin' => Hash::make('1234'),
                'status' => CardStatus::Active,
                'daily_limit' => 1000000.00,
                'monthly_limit' => 10000000.00,
                'international_enabled' => false,
                'online_enabled' => true,
                'contactless_enabled' => true,
                'issued_at' => now()->subMonths(10),
                'expires_at' => now()->addYears(2),
                'activated_at' => now()->subMonths(10),
            ],
            [
                'account_number' => '1000500001',
                'card_number' => '4200112233440006',
                'card_holder_name' => 'TARIQ AL-HALABI',
                'card_type' => CardType::Virtual,
                'card_brand' => CardBrand::Visa,
                'expiry_month' => 1,
                'expiry_year' => 2027,
                'cvv' => '113',
                'pin' => Hash::make('1234'),
                'status' => CardStatus::Active,
                'daily_limit' => 2500000.00,
                'monthly_limit' => 20000000.00,
                'international_enabled' => true,
                'online_enabled' => true,
                'contactless_enabled' => false,
                'issued_at' => now()->subMonths(4),
                'expires_at' => now()->addYears(1),
                'activated_at' => now()->subMonths(4),
            ],
        ];

        foreach ($cardsData as $item) {
            $account = $accounts->get($item['account_number']);
            if (!$account) {
                continue;
            }

            Card::updateOrCreate(
                ['card_number' => $item['card_number']],
                [
                    'card_holder_name' => $item['card_holder_name'],
                    'card_type' => $item['card_type'],
                    'card_brand' => $item['card_brand'],
                    'expiry_month' => $item['expiry_month'],
                    'expiry_year' => $item['expiry_year'],
                    'cvv' => $item['cvv'],
                    'pin' => $item['pin'],
                    'status' => $item['status'],
                    'account_id' => $account->id,
                    'customer_id' => $account->customer_id,
                    'daily_limit' => $item['daily_limit'],
                    'monthly_limit' => $item['monthly_limit'],
                    'international_enabled' => $item['international_enabled'],
                    'online_enabled' => $item['online_enabled'],
                    'contactless_enabled' => $item['contactless_enabled'],
                    'issued_at' => $item['issued_at'],
                    'expires_at' => $item['expires_at'],
                    'activated_at' => $item['activated_at'],
                ]
            );
        }
    }
}
