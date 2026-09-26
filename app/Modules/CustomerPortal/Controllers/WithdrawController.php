<?php

namespace App\Modules\CustomerPortal\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CustomerPortal\Requests\WithdrawRequest;
use App\Modules\CustomerPortal\Services\CustomerPortalService;
use App\Modules\Transactions\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class WithdrawController extends Controller
{
    public function __construct(
        protected CustomerPortalService $portalService,
        protected TransactionService $transactionService
    ) {}

    /**
     * Show withdrawal form.
     */
    public function create(Request $request): View
    {
        $user = $request->user();
        $accounts = $this->portalService->getCustomerAccounts($user);
        $selectedAccountId = $request->query('account_id', $accounts->first()?->id);

        return view('customer-portal::withdraw', [
            'accounts' => $accounts,
            'selectedAccountId' => $selectedAccountId,
        ]);
    }

    /**
     * Handle customer withdrawal.
     */
    public function store(WithdrawRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        try {
            // Verify ownership
            $account = $this->portalService->getCustomerAccount($user, (int) $validated['account_id']);
            $amount = (float) $validated['amount'];

            // Balance check
            if ($account->balance < $amount) {
                return back()
                    ->withInput()
                    ->with('error', __('Insufficient funds. Your available balance is :bal :curr.', [
                        'bal' => number_format((float) $account->balance, 2),
                        'curr' => $account->currency,
                    ]));
            }

            $method = $validated['method'];
            $desc = $validated['description'] ?: __('Withdrawal via :method', ['method' => ucfirst(str_replace('_', ' ', $method))]);

            $metadata = [
                'withdrawal_method' => $method,
                'channel' => 'Customer Web Portal',
                'pickup_branch' => $validated['pickup_branch'] ?? null,
                'beneficiary_bank' => $validated['beneficiary_bank'] ?? null,
                'beneficiary_iban' => $validated['beneficiary_iban'] ?? null,
            ];

            $transaction = $this->transactionService->withdrawal($account, $amount, $metadata);

            $transaction->update([
                'description' => $desc,
            ]);

            return redirect()->route('portal.dashboard')
                ->with('success', __('Successfully withdrawn :amount :currency from account :acc.', [
                    'amount' => number_format($amount, 2),
                    'currency' => $account->currency,
                    'acc' => $account->account_number,
                ]))
                ->with('transaction_reference', $transaction->transaction_reference);

        } catch (Throwable $e) {
            return back()
                ->withInput()
                ->with('error', __('Withdrawal failed: :message', ['message' => $e->getMessage()]));
        }
    }
}
