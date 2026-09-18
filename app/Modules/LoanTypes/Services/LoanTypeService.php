<?php

namespace App\Modules\LoanTypes\Services;

use App\Modules\LoanTypes\Contracts\LoanTypeRepositoryContract;
use App\Modules\LoanTypes\Models\LoanType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanTypeService
{
    public function __construct(private LoanTypeRepositoryContract $loanTypes) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->loanTypes->paginate($perPage);
    }

    public function create(array $data): LoanType
    {
        return DB::transaction(fn (): LoanType => $this->loanTypes->create($data));
    }

    public function update(LoanType $loanType, array $data): LoanType
    {
        return DB::transaction(fn (): LoanType => $this->loanTypes->update($loanType, $data));
    }

    public function delete(LoanType $loanType): bool
    {
        if ($loanType->loans()->exists()) {
            throw ValidationException::withMessages([
                'loan_type' => 'Loan types with existing loan applications cannot be deleted. Deactivate the type instead.',
            ]);
        }

        return DB::transaction(fn (): bool => $this->loanTypes->delete($loanType));
    }
}
