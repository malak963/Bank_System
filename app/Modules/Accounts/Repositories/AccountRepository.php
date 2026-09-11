<?php

namespace App\Modules\Accounts\Repositories;

use App\Modules\Accounts\Contracts\AccountRepositoryContract;
use App\Modules\Accounts\Enums\AccountStatus;
use App\Modules\Accounts\Models\Account;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class AccountRepository implements AccountRepositoryContract
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->filteredQuery($filters)
            ->with(['customer', 'accountType'])
            ->paginate($perPage)
            ->withQueryString();
    }

    public function summary(array $filters = []): array
    {
        $query = $this->filteredQuery($filters, false);

        return [
            'total' => (clone $query)->count(),
            'open' => (clone $query)->where('status', AccountStatus::Open->value)->count(),
            'frozen' => (clone $query)->where('status', AccountStatus::Frozen->value)->count(),
            'closed' => (clone $query)->where('status', AccountStatus::Closed->value)->count(),
        ];
    }

    public function create(array $data): Account
    {
        return Account::create($data);
    }

    public function update(Account $account, array $data): Account
    {
        $account->fill($data);
        $account->save();

        return $account->refresh();
    }

    private function filteredQuery(array $filters = [], bool $applySort = true): Builder
    {
        $query = Account::query();

        if (! empty($filters['search'])) {
            $like = '%'.mb_strtolower(trim($filters['search'])).'%';
            $query->where(function (Builder $query) use ($like): void {
                $query
                    ->whereRaw('LOWER(account_number) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(iban) LIKE ?', [$like])
                    ->orWhereHas('customer', function (Builder $query) use ($like): void {
                        $query
                            ->whereRaw('LOWER(customer_number) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(first_name) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(last_name) LIKE ?', [$like]);
                    });
            });
        }

        foreach (['status', 'account_type_id'] as $field) {
            if (! empty($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }

        if (($filters['sort'] ?? 'latest') === 'balance') {
            $query->orderByDesc('balance')->orderByDesc('id');
        } else {
            $query->latest('id');
        }

        return $applySort ? $query : $query->reorder();
    }
}
