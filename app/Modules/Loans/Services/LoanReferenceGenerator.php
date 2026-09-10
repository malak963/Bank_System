<?php

namespace App\Modules\Loans\Services;

use App\Modules\Loans\Models\Loan;

class LoanReferenceGenerator
{
    public function generate(): string
    {
        do {
            $reference = 'LN-'.now()->format('Y').'-'.str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Loan::query()->where('loan_reference', $reference)->exists());

        return $reference;
    }
}
