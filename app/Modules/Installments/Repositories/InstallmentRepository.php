<?php

namespace App\Modules\Installments\Repositories;

use App\Modules\Installments\Contracts\InstallmentRepositoryContract;
use App\Modules\Installments\Enums\InstallmentStatus;
use App\Modules\Installments\Models\Installment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class InstallmentRepository implements InstallmentRepositoryContract
{
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->filteredQuery($filters)
            ->with(['loan.customer', 'loan.loanType'])
            ->orderBy('due_date')
            ->orderBy('installment_number')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function summary(): array
    {
        $query = Installment::query();

        $activeBranchId = session('current_branch_id');
        if (! empty($activeBranchId)) {
            $query->whereHas('loan', function (Builder $q) use ($activeBranchId): void {
                $q->where('branch_id', $activeBranchId)
                  ->orWhereHas('customer', fn ($cq) => $cq->where('branch_id', $activeBranchId));
            });
        }

        return [
            'total' => (clone $query)->count(),
            'paid' => (clone $query)->where('status', InstallmentStatus::Paid->value)->count(),
            'overdue' => (clone $query)->where('status', '!=', InstallmentStatus::Paid->value)->whereDate('due_date', '<', today())->count(),
            'due_today' => (clone $query)->where('status', '!=', InstallmentStatus::Paid->value)->whereDate('due_date', today())->count(),
        ];
    }

    private function filteredQuery(array $filters): Builder
    {
        $query = Installment::query();

        $activeBranchId = $filters['branch_id'] ?? session('current_branch_id');
        if (! empty($activeBranchId)) {
            $query->whereHas('loan', function (Builder $q) use ($activeBranchId): void {
                $q->where('branch_id', $activeBranchId)
                  ->orWhereHas('customer', fn ($cq) => $cq->where('branch_id', $activeBranchId));
            });
        }

        if (! empty($filters['search'])) {
            $like = '%'.mb_strtolower(trim($filters['search'])).'%';
            $query->whereHas('loan', function (Builder $query) use ($like): void {
                $query->whereRaw('LOWER(loan_reference) LIKE ?', [$like])
                    ->orWhereHas('customer', function (Builder $query) use ($like): void {
                        $query->whereRaw('LOWER(customer_number) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(first_name) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(last_name) LIKE ?', [$like]);
                    });
            });
        }

        if (($filters['status'] ?? null) === 'overdue') {
            $query->where('status', '!=', InstallmentStatus::Paid->value)->whereDate('due_date', '<', today());
        } elseif (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['due_from'])) {
            $query->whereDate('due_date', '>=', $filters['due_from']);
        }

        if (! empty($filters['due_to'])) {
            $query->whereDate('due_date', '<=', $filters['due_to']);
        }

        return $query;
    }
}
