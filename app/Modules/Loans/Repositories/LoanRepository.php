<?php

namespace App\Modules\Loans\Repositories;

use App\Modules\Loans\Contracts\LoanRepositoryContract;
use App\Modules\Loans\Enums\LoanStatus;
use App\Modules\Loans\Models\Loan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class LoanRepository implements LoanRepositoryContract
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->filteredQuery($filters)
            ->with(['customer', 'loanType', 'account'])
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function summary(array $filters = []): array
    {
        $query = $this->filteredQuery($filters);

        return [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->where('status', LoanStatus::Pending->value)->count(),
            'active' => (clone $query)->whereIn('status', [LoanStatus::Disbursed->value, LoanStatus::Active->value])->count(),
            'paid_off' => (clone $query)->where('status', LoanStatus::PaidOff->value)->count(),
            'outstanding' => (float) (clone $query)->sum('outstanding_principal'),
        ];
    }

    public function create(array $data): Loan
    {
        return Loan::create($data);
    }

    public function update(Loan $loan, array $data): Loan
    {
        $loan->fill($data);
        $loan->save();

        return $loan->refresh();
    }

    private function filteredQuery(array $filters = []): Builder
    {
        $query = Loan::query();

        if (! empty($filters['search'])) {
            $like = '%'.mb_strtolower(trim($filters['search'])).'%';
            $query->where(function (Builder $query) use ($like): void {
                $query->whereRaw('LOWER(loan_reference) LIKE ?', [$like])
                    ->orWhereHas('customer', function (Builder $query) use ($like): void {
                        $query->whereRaw('LOWER(customer_number) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(first_name) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(last_name) LIKE ?', [$like]);
                    });
            });
        }

        foreach (['status', 'loan_type_id'] as $field) {
            if (! empty($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }

        return $query;
    }
}
