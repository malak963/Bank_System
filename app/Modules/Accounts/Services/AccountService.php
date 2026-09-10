<?php

namespace App\Modules\Accounts\Services;

use App\Modules\AccountTypes\Enums\AccountTypeStatus;
use App\Modules\AccountTypes\Models\AccountType;
use App\Modules\Accounts\Contracts\AccountRepositoryContract;
use App\Modules\Accounts\Enums\AccountStatus;
use App\Modules\Accounts\Models\Account;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AccountService
{
    public function __construct(
        private AccountRepositoryContract $accounts,
        private AccountNumberGenerator $numberGenerator,
    ) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->accounts->paginate($filters, $perPage);
    }

    public function summary(array $filters = []): array
    {
        return $this->accounts->summary($filters);
    }

    public function create(array $data): Account
    {
        return DB::transaction(function () use ($data): Account {
            $accountType = AccountType::query()
                ->whereKey($data['account_type_id'])
                ->where('status', AccountTypeStatus::Active->value)
                ->first();

            if ($accountType === null) {
                throw ValidationException::withMessages([
                    'account_type_id' => 'The selected account type is inactive.',
                ]);
            }

            $accountNumber = $this->numberGenerator->generate();

            return $this->accounts->create([
                'customer_id' => $data['customer_id'],
                'account_type_id' => $accountType->id,
                'account_number' => $accountNumber,
                'iban' => $this->numberGenerator->iban($accountNumber),
                'status' => AccountStatus::Open,
                'balance' => $data['initial_deposit'] ?? 0,
                'opened_at' => now(),
            ]);
        });
    }

    public function open(Account $account): Account
    {
        $this->assertStatus($account, AccountStatus::Closed, 'Only closed accounts can be opened.');

        return $this->transition($account, [
            'status' => AccountStatus::Open,
            'opened_at' => now(),
            'closed_at' => null,
            'frozen_at' => null,
            'freeze_reason' => null,
        ]);
    }

    public function close(Account $account): Account
    {
        if ($account->status === AccountStatus::Closed) {
            throw ValidationException::withMessages(['account' => 'The account is already closed.']);
        }

        if ((float) $account->balance !== 0.0) {
            throw ValidationException::withMessages(['account' => 'An account must have a zero balance before it can be closed.']);
        }

        return $this->transition($account, [
            'status' => AccountStatus::Closed,
            'closed_at' => now(),
            'frozen_at' => null,
            'freeze_reason' => null,
        ]);
    }

    public function freeze(Account $account, ?string $reason = null): Account
    {
        $this->assertStatus($account, AccountStatus::Open, 'Only open accounts can be frozen.');

        return $this->transition($account, [
            'status' => AccountStatus::Frozen,
            'frozen_at' => now(),
            'freeze_reason' => $reason,
        ]);
    }

    public function reactivate(Account $account): Account
    {
        $this->assertStatus($account, AccountStatus::Frozen, 'Only frozen accounts can be reactivated.');

        return $this->transition($account, [
            'status' => AccountStatus::Open,
            'frozen_at' => null,
            'freeze_reason' => null,
        ]);
    }

    private function transition(Account $account, array $data): Account
    {
        return DB::transaction(fn (): Account => $this->accounts->update($account, $data));
    }

    private function assertStatus(Account $account, AccountStatus $expected, string $message): void
    {
        if ($account->status !== $expected) {
            throw ValidationException::withMessages(['account' => $message]);
        }
    }
}
