<?php

namespace App\Modules\Branches\Repositories;

use App\Modules\Branches\Contracts\BranchRepositoryContract;
use App\Modules\Branches\Enums\BranchStatus;
use App\Modules\Branches\Models\Branch;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class BranchRepository implements BranchRepositoryContract
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->filteredQuery($filters)
            ->with(['manager'])
            ->paginate($perPage)
            ->withQueryString();
    }

    public function summary(array $filters = []): array
    {
        $query = $this->filteredQuery($filters, false);

        return [
            'total' => (clone $query)->count(),
            'open' => (clone $query)->where('status', BranchStatus::Open->value)->count(),
            'closed' => (clone $query)->where('status', BranchStatus::Closed->value)->count(),
        ];
    }

    public function create(array $data): Branch
    {
        return Branch::create($data);
    }

    public function update(Branch $branch, array $data): Branch
    {
        $branch->fill($data);
        $branch->save();

        return $branch->refresh();
    }

    private function filteredQuery(array $filters = [], bool $applySort = true): Builder
    {
        $query = Branch::query();

        if (! empty($filters['search'])) {
            $like = '%'.mb_strtolower(trim($filters['search'])).'%';
            $query->where(function (Builder $query) use ($like): void {
                $query
                    ->whereRaw('LOWER(code) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(name) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(address) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(phone) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(email) LIKE ?', [$like]);
            });
        }

        foreach (['status'] as $field) {
            if (! empty($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }

        if (($filters['sort'] ?? 'latest') === 'name') {
            $query->orderBy('name')->orderByDesc('id');
        } else {
            $query->latest('id');
        }

        return $applySort ? $query : $query->reorder();
    }
}
