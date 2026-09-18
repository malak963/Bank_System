<?php

namespace App\Modules\Loans\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounts\Models\Account;
use App\Modules\Installments\Enums\InstallmentStatus;
use App\Modules\LoanTypes\Models\LoanType;
use App\Modules\Loans\Enums\LoanPaymentMethod;
use App\Modules\Loans\Enums\LoanStatus;
use App\Modules\Loans\Models\Loan;
use App\Modules\Loans\Requests\ApproveLoanRequest;
use App\Modules\Loans\Requests\IndexLoanRequest;
use App\Modules\Loans\Requests\RejectLoanRequest;
use App\Modules\Loans\Requests\StoreLoanPaymentRequest;
use App\Modules\Loans\Requests\StoreLoanRequest;
use App\Modules\Loans\Services\LoanService;
use App\Modules\Customers\Models\Customer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class LoanController extends Controller
{
    public function __construct(private LoanService $service) {}

    public function index(IndexLoanRequest $request): View
    {
        $filters = $request->filters();

        return view('loans::index', [
            'loans' => $this->service->paginate($filters),
            'summary' => $this->service->summary($filters),
            'filters' => $filters,
            'statuses' => LoanStatus::cases(),
            'loanTypes' => LoanType::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('loans::create', [
            'customers' => Customer::query()
                ->whereHas('accounts', fn ($query) => $query->where('status', 'open'))
                ->with(['accounts' => fn ($query) => $query->where('status', 'open')->with('accountType')])
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get(),
            'loanTypes' => LoanType::query()->where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreLoanRequest $request): RedirectResponse
    {
        $loan = $this->service->create($request->validated());

        return redirect()->route('loans.show', $loan)->with('status', 'Loan application created.');
    }

    public function show(Loan $loan): View
    {
        $loan->load(['customer.user', 'account.accountType', 'loanType', 'approver', 'installments', 'payments.receiver', 'payments.allocations.installment']);

        return view('loans::show', [
            'loan' => $loan,
            'paymentMethods' => LoanPaymentMethod::cases(),
            'installmentStatuses' => InstallmentStatus::cases(),
        ]);
    }

    public function approve(ApproveLoanRequest $request, Loan $loan): RedirectResponse
    {
        $this->service->approve($loan, $request->validated(), (int) $request->user()->id);

        return redirect()->route('loans.show', $loan)->with('status', 'Loan approved and installment schedule created.');
    }

    public function reject(RejectLoanRequest $request, Loan $loan): RedirectResponse
    {
        $this->service->reject($loan, (string) $request->validated('rejection_reason'));

        return redirect()->route('loans.show', $loan)->with('status', 'Loan application rejected.');
    }

    public function disburse(Loan $loan): RedirectResponse
    {
        $this->service->disburse($loan);

        return redirect()->route('loans.show', $loan)->with('status', 'Loan disbursed to the linked account.');
    }

    public function payment(StoreLoanPaymentRequest $request, Loan $loan): RedirectResponse
    {
        $this->service->recordPayment($loan, $request->validated(), (int) $request->user()->id);

        return redirect()->route('loans.show', $loan)->with('status', 'Loan payment recorded.');
    }
}
