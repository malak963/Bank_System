<?php

namespace App\Modules\CustomerPortal\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CustomerPortal\Requests\DepositRequest;
use App\Modules\CustomerPortal\Services\CustomerPortalService;
use App\Modules\Transactions\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class DepositController extends Controller
{
    public function __construct(
        protected CustomerPortalService $portalService,
        protected TransactionService $transactionService
    ) {}

    /**
     * Show deposit form.
     */
    public function create(Request $request): View
    {
        $user = $request->user();
        $accounts = $this->portalService->getCustomerAccounts($user);
        $selectedAccountId = $request->query('account_id', $accounts->first()?->id);

        return view('customer-portal::deposit', [
            'accounts' => $accounts,
            'selectedAccountId' => $selectedAccountId,
        ]);
    }

    /**
     * Handle incoming deposit.
     */
    public function store(DepositRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        try {
            // Verify ownership
            $account = $this->portalService->getCustomerAccount($user, (int) $validated['account_id']);

            $amount = (float) $validated['amount'];
            $method = $validated['method'];
            $desc = $validated['description'] ?: __('Deposit via :method', ['method' => ucfirst(str_replace('_', ' ', $method))]);

            $metadata = [
                'deposit_method' => $method,
                'channel' => 'Customer Web Portal',
                'reference_note' => $validated['reference_note'] ?? null,
                'card_last4' => !empty($validated['card_number']) ? substr(preg_replace('/\D/', '', $validated['card_number']), -4) : null,
            ];

            $transaction = $this->transactionService->deposit($account, $amount, $metadata);

            // Update description if custom
            $transaction->update([
                'description' => $desc,
                'reference_number' => $validated['reference_note'] ?? $transaction->transaction_reference,
            ]);

            return redirect()->route('portal.dashboard')
                ->with('success', __('Successfully deposited :amount :currency into account :acc.', [
                    'amount' => number_format($amount, 2),
                    'currency' => $account->currency,
                    'acc' => $account->account_number,
                ]))
                ->with('transaction_reference', $transaction->transaction_reference);

        } catch (Throwable $e) {
            return back()
                ->withInput()
                ->with('error', __('Deposit failed: :message', ['message' => $e->getMessage()]));
        }
    }
}
