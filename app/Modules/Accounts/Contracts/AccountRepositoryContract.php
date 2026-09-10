<?php

namespace App\Modules\Accounts\Contracts;

use App\Modules\Accounts\Models\Account;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AccountRepositoryContract
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function summary(array $filters = []): array;

    public function create(array $data): Account;

    public function update(Account $account, array $data): Account;
}
