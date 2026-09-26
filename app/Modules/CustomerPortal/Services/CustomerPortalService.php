<?php

namespace App\Modules\CustomerPortal\Services;

use App\Models\User;
use App\Modules\Accounts\Enums\AccountStatus;
use App\Modules\Accounts\Models\Account;
use App\Modules\AccountTypes\Enums\AccountTypeStatus;
use App\Modules\AccountTypes\Models\AccountType;
use App\Modules\BillsPayments\Enums\BillStatus;
use App\Modules\BillsPayments\Enums\BillType;
use App\Modules\BillsPayments\Models\Bill;
use App\Modules\Branches\Enums\BranchStatus;
use App\Modules\Branches\Models\Branch;
use App\Modules\Customers\Enums\CustomerStatus;
use App\Modules\Customers\Enums\IdentityDocumentType;
use App\Modules\Customers\Enums\KycStatus;
use App\Modules\Customers\Enums\RiskLevel;
use App\Modules\Customers\Models\Customer;
use App\Modules\Transactions\Enums\TransactionStatus;
use App\Modules\Transactions\Enums\TransactionType;
use App\Modules\Transactions\Models\Transaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerPortalService
{
    /**
     * Ensure the user has an associated Customer profile and at least one Account.
     */
    public function ensureCustomerProfile(User $user): Customer
    {
        return DB::transaction(function () use ($user) {
            // 1. Get or create Customer
            $customer = Customer::where('user_id', $user->id)->first();

            if (!$customer) {
                // Ensure default branch exists
                $branch = Branch::first();
                if (!$branch) {
                    $branch = Branch::create([
                        'code' => 'BR-001',
                        'name' => 'Main Downtown Branch',
                        'address' => '100 Financial District, Central Avenue',
                        'phone' => '+963112233445',
                        'email' => 'downtown@bank.com',
                        'status' => BranchStatus::Open,
                        'opened_at' => now()->subYears(2),
                    ]);
                }

                $nameParts = explode(' ', trim($user->name), 2);
                $firstName = $nameParts[0] ?? 'Valued';
                $lastName = $nameParts[1] ?? 'Customer';

                $customer = Customer::create([
                    'user_id' => $user->id,
                    'branch_id' => $branch->id,
                    'customer_number' => 'CUS' . date('Y') . str_pad((string)$user->id, 5, '0', STR_PAD_LEFT),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'date_of_birth' => '1995-05-15',
                    'national_id' => 'NAT-' . rand(10000000, 99999999),
                    'identity_document_type' => IdentityDocumentType::NationalId,
                    'identity_document_number' => 'ID-' . rand(100000, 999999),
                    'identity_document_country' => 'SY',
                    'identity_document_expires_at' => now()->addYears(5),
                    'phone_number' => $user->phone ?? '+963944444444',
                    'address' => 'Customer Residential Street, Suite 10',
                    'status' => CustomerStatus::Active,
                    'kyc_status' => KycStatus::Approved,
                    'risk_level' => RiskLevel::Low,
                ]);
            }

            // 2. Ensure customer has at least one account
            if ($customer->accounts()->count() === 0) {
                $accountType = AccountType::where('code', 'CURRENT')->first()
                    ?? AccountType::first()
                    ?? AccountType::create([
                        'name' => 'Current Checking Account',
                        'code' => 'CURRENT',
                        'description' => 'Standard checking account for daily banking',
                        'currency' => 'USD',
                        'status' => AccountTypeStatus::Active,
                    ]);

                $accountNumber = '2026' . str_pad((string)$customer->id, 4, '0', STR_PAD_LEFT) . rand(1000, 9999);
                $iban = 'SY98MDAD' . $accountNumber . rand(1000, 9999);

                $account = Account::create([
                    'branch_id' => $customer->branch_id ?? 1,
                    'customer_id' => $customer->id,
                    'account_type_id' => $accountType->id,
                    'account_number' => $accountNumber,
                    'iban' => $iban,
                    'status' => AccountStatus::Open,
                    'balance' => 2500.00, // Initial funding so user can test transfers/withdrawals/bills immediately
                    'currency' => 'USD',
                    'opened_at' => now(),
                ]);

                // Create initial deposit record for transparency
                Transaction::create([
                    'transaction_reference' => 'TXN' . date('Ymd') . rand(100000, 999999),
                    'branch_id' => $account->branch_id,
                    'customer_id' => $customer->id,
                    'account_id' => $account->id,
                    'transaction_type' => TransactionType::Deposit,
                    'amount' => 2500.00,
                    'balance_before' => 0.00,
                    'balance_after' => 2500.00,
                    'currency' => 'USD',
                    'status' => TransactionStatus::Completed,
                    'description' => 'Initial welcome deposit & account funding',
                    'category' => 'deposit',
                    'transactable_type' => Account::class,
                    'transactable_id' => $account->id,
                    'processed_at' => now(),
                    'created_by' => $user->id,
                ]);
            }

            // 3. Ensure sample pending bills exist for testing the invoice feature
            $billCount = Bill::where('customer_id', $customer->id)->count();
            if ($billCount === 0) {
                $primaryAccount = $customer->accounts()->first();

                Bill::create([
                    'bill_reference' => 'INV-ELEC-' . rand(10000, 99999),
                    'customer_id' => $customer->id,
                    'account_id' => $primaryAccount?->id,
                    'bill_type' => BillType::ELECTRICITY,
                    'provider_name' => 'Metro Electric Power Grid',
                    'provider_account_number' => 'ELEC-98721',
                    'amount' => 145.50,
                    'currency' => 'USD',
                    'due_date' => now()->addDays(7)->toDateString(),
                    'status' => BillStatus::PENDING,
                    'description' => 'Residential Electricity Invoice for current month',
                ]);

                Bill::create([
                    'bill_reference' => 'INV-WATER-' . rand(10000, 99999),
                    'customer_id' => $customer->id,
                    'account_id' => $primaryAccount?->id,
                    'bill_type' => BillType::WATER,
                    'provider_name' => 'Municipal Clean Water Utility',
                    'provider_account_number' => 'WTR-44120',
                    'amount' => 38.00,
                    'currency' => 'USD',
                    'due_date' => now()->addDays(12)->toDateString(),
                    'status' => BillStatus::PENDING,
                    'description' => 'Domestic Water Consumption Services',
                ]);

                Bill::create([
                    'bill_reference' => 'INV-NET-' . rand(10000, 99999),
                    'customer_id' => $customer->id,
                    'account_id' => $primaryAccount?->id,
                    'bill_type' => BillType::INTERNET,
                    'provider_name' => 'FiberNet High-Speed Broadband',
                    'provider_account_number' => 'FIBER-88219',
                    'amount' => 65.00,
                    'currency' => 'USD',
                    'due_date' => now()->addDays(3)->toDateString(),
                    'status' => BillStatus::PENDING,
                    'description' => 'Monthly Unlimited Fiber Gigabit Plan (1 Gbps)',
                ]);
            }

            return $customer->fresh(['accounts.accountType', 'accounts.branch']);
        });
    }

    /**
     * Get all active accounts for the authenticated customer.
     */
    public function getCustomerAccounts(User $user): Collection
    {
        $customer = $this->ensureCustomerProfile($user);
        return $customer->accounts()
            ->with(['accountType', 'branch'])
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * Get a specific account belonging to the authenticated user.
     */
    public function getCustomerAccount(User $user, int $accountId): Account
    {
        $customer = $this->ensureCustomerProfile($user);
        return $customer->accounts()
            ->with(['accountType', 'branch'])
            ->findOrFail($accountId);
    }

    /**
     * Get overall portal statistics for dashboard.
     */
    public function getDashboardMetrics(User $user): array
    {
        $customer = $this->ensureCustomerProfile($user);
        $accounts = $customer->accounts;
        $accountIds = $accounts->pluck('id')->toArray();

        $totalBalance = (float) $accounts->sum('balance');
        $currency = $accounts->first()?->currency ?? 'USD';

        $totalDeposits = (float) Transaction::whereIn('account_id', $accountIds)
            ->where('transaction_type', TransactionType::Deposit->value)
            ->where('status', TransactionStatus::Completed->value)
            ->sum('amount');

        $totalWithdrawals = (float) Transaction::whereIn('account_id', $accountIds)
            ->whereIn('transaction_type', [TransactionType::Withdrawal->value, TransactionType::AtmWithdrawal->value])
            ->where('status', TransactionStatus::Completed->value)
            ->sum('amount');

        $pendingBillsCount = Bill::where('customer_id', $customer->id)
            ->where('status', BillStatus::PENDING->value)
            ->count();

        $pendingBillsAmount = (float) Bill::where('customer_id', $customer->id)
            ->where('status', BillStatus::PENDING->value)
            ->sum('amount');

        $paidBillsCount = Bill::where('customer_id', $customer->id)
            ->where('status', BillStatus::COMPLETED->value)
            ->count();

        $recentTransactions = Transaction::whereIn('account_id', $accountIds)
            ->with('account')
            ->latest('created_at')
            ->limit(7)
            ->get();

        $pendingBills = Bill::where('customer_id', $customer->id)
            ->where('status', BillStatus::PENDING->value)
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get();

        return [
            'total_balance' => $totalBalance,
            'currency' => $currency,
            'accounts_count' => $accounts->count(),
            'total_deposits' => $totalDeposits,
            'total_withdrawals' => $totalWithdrawals,
            'pending_bills_count' => $pendingBillsCount,
            'pending_bills_amount' => $pendingBillsAmount,
            'paid_bills_count' => $paidBillsCount,
            'recent_transactions' => $recentTransactions,
            'pending_bills' => $pendingBills,
            'accounts' => $accounts,
            'customer' => $customer,
        ];
    }
}
