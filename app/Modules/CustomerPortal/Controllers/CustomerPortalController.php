<?php

namespace App\Modules\CustomerPortal\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounts\Enums\AccountStatus;
use App\Modules\Accounts\Models\Account;
use App\Modules\AccountTypes\Enums\AccountTypeStatus;
use App\Modules\AccountTypes\Models\AccountType;
use App\Modules\CustomerPortal\Services\CustomerPortalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerPortalController extends Controller
{
    public function __construct(
        protected CustomerPortalService $portalService
    ) {}

    /**
     * Customer Portal Dashboard Overview.
     */
    public function dashboard(Request $request): View
    {
        $user = $request->user();
        $data = $this->portalService->getDashboardMetrics($user);
        $accountTypes = AccountType::where('status', AccountTypeStatus::Active)->get();

        return view('customer-portal::dashboard', array_merge($data, [
            'availableAccountTypes' => $accountTypes,
        ]));
    }

    /**
     * Create an additional secondary account (e.g. Savings or Secondary Current).
     */
    public function createSecondaryAccount(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'account_type_id' => ['required', 'exists:account_types,id'],
            'currency' => ['required', 'string', 'in:USD,EUR,SYP,AED,SAR'],
            'initial_deposit' => ['nullable', 'numeric', 'min:0', 'max:50000'],
        ]);

        $user = $request->user();
        $customer = $this->portalService->ensureCustomerProfile($user);

        $accountNumber = '2026' . str_pad((string)$customer->id, 4, '0', STR_PAD_LEFT) . rand(1000, 9999);
        $iban = 'SY98MDAD' . $accountNumber . rand(1000, 9999);
        $initialDeposit = (float) ($validated['initial_deposit'] ?? 0);

        $account = Account::create([
            'branch_id' => $customer->branch_id ?? 1,
            'customer_id' => $customer->id,
            'account_type_id' => $validated['account_type_id'],
            'account_number' => $accountNumber,
            'iban' => $iban,
            'status' => AccountStatus::Open,
            'balance' => $initialDeposit,
            'currency' => $validated['currency'],
            'opened_at' => now(),
        ]);

        return redirect()->route('portal.dashboard')
            ->with('success', __('New account :number opened successfully!', ['number' => $accountNumber]));
    }
}
