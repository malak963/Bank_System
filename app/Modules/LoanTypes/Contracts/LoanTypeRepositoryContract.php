<?php

namespace App\Modules\LoanTypes\Contracts;

use App\Modules\LoanTypes\Models\LoanType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LoanTypeRepositoryContract
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): LoanType;

    public function update(LoanType $loanType, array $data): LoanType;

    public function delete(LoanType $loanType): bool;
}
