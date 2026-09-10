<?php

namespace App\Modules\Loans\Contracts;

use App\Modules\Loans\Models\Loan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LoanRepositoryContract
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function summary(array $filters = []): array;

    public function create(array $data): Loan;

    public function update(Loan $loan, array $data): Loan;
}
