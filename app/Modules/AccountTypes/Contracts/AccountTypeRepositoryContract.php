<?php

namespace App\Modules\AccountTypes\Contracts;

use App\Modules\AccountTypes\Models\AccountType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AccountTypeRepositoryContract
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): AccountType;

    public function create(array $data): AccountType;

    public function update(AccountType $accountType, array $data): AccountType;

    public function delete(AccountType $accountType): bool;
}
