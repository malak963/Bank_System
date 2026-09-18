<?php

namespace App\Modules\AccountTypes\Repositories;

use App\Modules\AccountTypes\Contracts\AccountTypeRepositoryContract;
use App\Modules\AccountTypes\Models\AccountType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AccountTypeRepository implements AccountTypeRepositoryContract
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return AccountType::query()
            ->withCount('accounts')
            ->orderByDesc('status')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findById(int $id): AccountType
    {
        return AccountType::findOrFail($id);
    }

    public function create(array $data): AccountType
    {
        return AccountType::create($data);
    }

    public function update(AccountType $accountType, array $data): AccountType
    {
        $accountType->fill($data);
        $accountType->save();

        return $accountType->refresh();
    }

    public function delete(AccountType $accountType): bool
    {
        return (bool) $accountType->delete();
    }
}
