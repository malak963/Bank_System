<?php

namespace App\Modules\Accounts\Services;

use App\Modules\Accounts\Models\Account;

class AccountNumberGenerator
{
    public function generate(): string
    {
        do {
            $number = '10'.str_pad((string) random_int(0, 9999999999), 10, '0', STR_PAD_LEFT);
        } while (Account::query()->where('account_number', $number)->exists());

        return $number;
    }

    public function iban(string $accountNumber): string
    {
        $bban = '1000'.'000'.str_pad($accountNumber, 15, '0', STR_PAD_LEFT);
        $checkDigits = 98 - $this->modulo97($bban.'2824'.'00');

        return 'SY'.str_pad((string) $checkDigits, 2, '0', STR_PAD_LEFT).$bban;
    }

    private function modulo97(string $value): int
    {
        $remainder = 0;

        foreach (str_split($value) as $digit) {
            $remainder = (($remainder * 10) + (int) $digit) % 97;
        }

        return $remainder;
    }
}
