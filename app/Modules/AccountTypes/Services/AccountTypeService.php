<?php

namespace App\Modules\AccountTypes\Services;

use App\Modules\AccountTypes\Contracts\AccountTypeRepositoryContract;
use App\Modules\AccountTypes\Models\AccountType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AccountTypeService
{
    public function __construct(
        private AccountTypeRepositoryContract $accountTypes
    ) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->accountTypes->paginate($perPage);
    }

    public function create(array $data): AccountType
    {
        return DB::transaction(fn (): AccountType => $this->accountTypes->create($data));
    }

    public function update(AccountType $accountType, array $data): AccountType
    {
        return DB::transaction(fn (): AccountType => $this->accountTypes->update($accountType, $data));
    }

    public function delete(AccountType $accountType): bool
    {
        if ($accountType->accounts()->exists()) {
            throw ValidationException::withMessages([
                'account_type' => 'Account types with existing accounts cannot be deleted. Deactivate the type instead.',
            ]);
        }

        return DB::transaction(fn (): bool => $this->accountTypes->delete($accountType));
    }
}
