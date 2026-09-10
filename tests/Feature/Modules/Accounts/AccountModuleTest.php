<?php

namespace Tests\Feature\Modules\Accounts;

use App\Models\User;
use App\Modules\AccountTypes\Models\AccountType;
use App\Modules\Accounts\Enums\AccountStatus;
use App\Modules\Accounts\Models\Account;
use App\Modules\Customers\Database\Factories\CustomerFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_routes_require_authentication(): void
    {
        $this->get('/accounts')->assertRedirect('/login');
    }

    public function test_account_creation_generates_unique_account_number_and_valid_iban(): void
    {
        $actor = User::factory()->create();
        $customer = CustomerFactory::new()->create();
        $accountType = AccountType::query()->firstOrFail();

        $response = $this->actingAs($actor)->post(route('accounts.store'), [
            'customer_id' => $customer->id,
            'account_type_id' => $accountType->id,
            'initial_deposit' => '1250.50',
        ]);

        $account = Account::query()->firstOrFail();

        $response->assertRedirect(route('accounts.show', $account));
        $this->assertSame(12, strlen($account->account_number));
        $this->assertSame(26, strlen($account->iban));
        $this->assertIbanChecksum($account->iban);
        $this->assertDatabaseHas('accounts', [
            'customer_id' => $customer->id,
            'account_type_id' => $accountType->id,
            'status' => AccountStatus::Open->value,
            'balance' => 1250.50,
        ]);
    }

    public function test_account_can_be_frozen_reactivated_and_closed(): void
    {
        $actor = User::factory()->create();
        $account = Account::factory()->create(['balance' => 0]);

        $this->actingAs($actor)
            ->post(route('accounts.freeze', $account), ['freeze_reason' => 'Compliance review'])
            ->assertRedirect(route('accounts.show', $account));
        $this->assertSame(AccountStatus::Frozen, $account->refresh()->status);

        $this->actingAs($actor)
            ->post(route('accounts.reactivate', $account))
            ->assertRedirect(route('accounts.show', $account));
        $this->assertSame(AccountStatus::Open, $account->refresh()->status);

        $this->actingAs($actor)
            ->post(route('accounts.close', $account))
            ->assertRedirect(route('accounts.show', $account));
        $this->assertSame(AccountStatus::Closed, $account->refresh()->status);
    }

    public function test_non_zero_balance_cannot_be_closed(): void
    {
        $actor = User::factory()->create();
        $account = Account::factory()->create(['balance' => 25]);

        $this->actingAs($actor)
            ->from(route('accounts.show', $account))
            ->post(route('accounts.close', $account))
            ->assertRedirect(route('accounts.show', $account))
            ->assertSessionHasErrors('account');

        $this->assertSame(AccountStatus::Open, $account->refresh()->status);
    }

    private function assertIbanChecksum(string $iban): void
    {
        $rearranged = substr($iban, 4).'2824'.substr($iban, 2, 2);
        $remainder = 0;

        foreach (str_split($rearranged) as $digit) {
            $remainder = (($remainder * 10) + (int) $digit) % 97;
        }

        $this->assertSame(1, $remainder);
    }
}
