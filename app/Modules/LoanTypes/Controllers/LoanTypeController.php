<?php

namespace App\Modules\LoanTypes\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\LoanTypes\Enums\LoanTypeStatus;
use App\Modules\LoanTypes\Models\LoanType;
use App\Modules\LoanTypes\Requests\StoreLoanTypeRequest;
use App\Modules\LoanTypes\Requests\UpdateLoanTypeRequest;
use App\Modules\LoanTypes\Services\LoanTypeService;
use App\Modules\Loans\Enums\InterestMethod;
use App\Modules\Loans\Enums\RepaymentFrequency;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class LoanTypeController extends Controller
{
    public function __construct(private LoanTypeService $service) {}

    public function index(): View
    {
        return view('loan_types::index', ['loanTypes' => $this->service->paginate()]);
    }

    public function create(): View
    {
        return view('loan_types::create', $this->formData());
    }

    public function store(StoreLoanTypeRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('loan-types.index')->with('status', 'Loan type created.');
    }

    public function edit(LoanType $loan_type): View
    {
        return view('loan_types::edit', [
            'loanType' => $loan_type,
            ...$this->formData(),
        ]);
    }

    public function update(UpdateLoanTypeRequest $request, LoanType $loan_type): RedirectResponse
    {
        $this->service->update($loan_type, $request->validated());

        return redirect()->route('loan-types.index')->with('status', 'Loan type updated.');
    }

    public function destroy(LoanType $loan_type): RedirectResponse
    {
        $this->service->delete($loan_type);

        return redirect()->route('loan-types.index')->with('status', 'Loan type deleted.');
    }

    private function formData(): array
    {
        return [
            'statuses' => LoanTypeStatus::cases(),
            'interestMethods' => InterestMethod::cases(),
            'frequencies' => RepaymentFrequency::cases(),
        ];
    }
}
