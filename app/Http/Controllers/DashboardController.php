<?php

namespace App\Http\Controllers;

use App\Modules\Accounts\Enums\AccountStatus;
use App\Modules\Accounts\Models\Account;
use App\Modules\Customers\Models\Customer;
use App\Modules\Installments\Enums\InstallmentStatus;
use App\Modules\Installments\Models\Installment;
use App\Modules\Loans\Enums\LoanStatus;
use App\Modules\Loans\Models\Loan;
use App\Modules\Loans\Models\LoanPayment;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Throwable;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $empty = $this->emptyData();

        try {
            if (! $this->hasBankingTables()) {
                return view('dashboard', $empty + ['databaseReady' => false]);
            }

            $now = now();
            $openStatuses = [LoanStatus::Disbursed->value, LoanStatus::Active->value];
            $unpaidStatuses = [InstallmentStatus::Pending->value, InstallmentStatus::PartiallyPaid->value];

            $metrics = [
                'customers' => Customer::query()->count(),
                'open_accounts' => Account::query()->where('status', AccountStatus::Open->value)->count(),
                'available_balance' => (float) Account::query()->where('status', AccountStatus::Open->value)->sum('balance'),
                'pending_loans' => Loan::query()->whereIn('status', [LoanStatus::Pending->value, LoanStatus::UnderReview->value])->count(),
                'active_loans' => Loan::query()->whereIn('status', $openStatuses)->count(),
                'outstanding_principal' => (float) Loan::query()->whereIn('status', $openStatuses)->sum('outstanding_principal'),
                'due_this_month' => (float) Installment::query()->whereIn('status', $unpaidStatuses)->whereBetween('due_date', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])->sum('amount_due'),
                'collected_this_month' => (float) LoanPayment::query()->whereBetween('paid_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])->sum('amount'),
            ];

            $recentLoans = Loan::query()
                ->with(['customer', 'loanType'])
                ->latest('created_at')
                ->limit(6)
                ->get();

            $upcomingInstallments = Installment::query()
                ->with(['loan.customer', 'loan.loanType'])
                ->whereIn('status', $unpaidStatuses)
                ->orderBy('due_date')
                ->limit(6)
                ->get();

            $portfolio = [
                'active' => Loan::query()->whereIn('status', $openStatuses)->count(),
                'pending' => Loan::query()->whereIn('status', [LoanStatus::Pending->value, LoanStatus::UnderReview->value])->count(),
                'paid_off' => Loan::query()->where('status', LoanStatus::PaidOff->value)->count(),
                'rejected' => Loan::query()->where('status', LoanStatus::Rejected->value)->count(),
            ];

            return view('dashboard', [
                'databaseReady' => true,
                'metrics' => $metrics,
                'recentLoans' => $recentLoans,
                'upcomingInstallments' => $upcomingInstallments,
                'portfolio' => $portfolio,
            ]);
        } catch (Throwable) {
            return view('dashboard', $empty + ['databaseReady' => false]);
        }
    }

    private function hasBankingTables(): bool
    {
        return Schema::hasTable('customers')
            && Schema::hasTable('accounts')
            && Schema::hasTable('loans')
            && Schema::hasTable('installments')
            && Schema::hasTable('loan_payments');
    }

    private function emptyData(): array
    {
        return [
            'metrics' => [
                'customers' => 0,
                'open_accounts' => 0,
                'available_balance' => 0,
                'pending_loans' => 0,
                'active_loans' => 0,
                'outstanding_principal' => 0,
                'due_this_month' => 0,
                'collected_this_month' => 0,
            ],
            'recentLoans' => new Collection(),
            'upcomingInstallments' => new Collection(),
            'portfolio' => ['active' => 0, 'pending' => 0, 'paid_off' => 0, 'rejected' => 0],
        ];
    }
}
