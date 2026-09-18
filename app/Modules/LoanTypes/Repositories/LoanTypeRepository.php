<?php

namespace App\Modules\LoanTypes\Repositories;

use App\Modules\LoanTypes\Contracts\LoanTypeRepositoryContract;
use App\Modules\LoanTypes\Models\LoanType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LoanTypeRepository implements LoanTypeRepositoryContract
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return LoanType::query()
            ->withCount('loans')
            ->orderByRaw("CASE status WHEN 'active' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function create(array $data): LoanType
    {
        return LoanType::create($data);
    }

    public function update(LoanType $loanType, array $data): LoanType
    {
        $loanType->fill($data);
        $loanType->save();

        return $loanType->refresh();
    }

    public function delete(LoanType $loanType): bool
    {
        return (bool) $loanType->delete();
    }
}
