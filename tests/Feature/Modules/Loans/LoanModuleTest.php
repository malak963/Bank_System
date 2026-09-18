<?php

namespace Tests\Feature\Modules\Loans;

use App\Models\User;
use App\Modules\Accounts\Models\Account;
use App\Modules\AccountTypes\Models\AccountType;
use App\Modules\Customers\Database\Factories\CustomerFactory;
use App\Modules\LoanTypes\Models\LoanType;
use App\Modules\Loans\Enums\LoanStatus;
use App\Modules\Loans\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_loan_routes_require_authentication(): void
    {
        $this->get('/loans')->assertRedirect('/login');
    }

    public function test_loan_is_approved_disbursed_and_repaid_with_an_amortization_schedule(): void
    {
        $actor = User::factory()->create();
        $customer = CustomerFactory::new()->create();
        $account = Account::factory()->create([
            'customer_id' => $customer->id,
            'account_type_id' => AccountType::query()->firstOrFail()->id,
        ]);
        $loanType = LoanType::query()->firstOrFail();

        $this->actingAs($actor)->post(route('loans.store'), [
            'customer_id' => $customer->id,
            'account_id' => $account->id,
            'loan_type_id' => $loanType->id,
            'requested_amount' => 150000,
            'term_months' => 12,
            'purpose' => 'Education expenses',
        ])->assertRedirect();

        $loan = Loan::query()->firstOrFail();

        $this->actingAs($actor)->post(route('loans.approve', $loan), [
            'approved_amount' => 150000,
            'first_payment_date' => today()->addMonth()->toDateString(),
        ])->assertRedirect(route('loans.show', $loan));

        $this->assertSame(12, $loan->refresh()->installments()->count());
        $this->assertGreaterThan(0, (float) $loan->total_interest);
        $this->assertSame(LoanStatus::Approved, $loan->status);

        $this->actingAs($actor)->post(route('loans.disburse', $loan))->assertRedirect(route('loans.show', $loan));
        $this->assertSame(LoanStatus::Active, $loan->refresh()->status);
        $this->assertSame(150000.0, (float) $account->refresh()->balance);

        $firstInstallment = $loan->installments()->firstOrFail();
        $this->actingAs($actor)->post(route('loans.payments.store', $loan), [
            'amount' => $firstInstallment->amount_due,
            'payment_method' => 'account_debit',
            'paid_at' => now()->format('Y-m-d H:i:s'),
        ])->assertRedirect(route('loans.show', $loan));

        $this->assertGreaterThan(0, (float) $loan->refresh()->total_paid);
        $this->assertDatabaseHas('loan_payments', ['loan_id' => $loan->id]);
        $this->assertDatabaseHas('loan_payment_allocations', ['installment_id' => $firstInstallment->id]);
    }
}
