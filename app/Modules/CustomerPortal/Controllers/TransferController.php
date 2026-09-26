<?php

namespace App\Modules\CustomerPortal\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounts\Models\Account;
use App\Modules\CustomerPortal\Requests\TransferRequest;
use App\Modules\CustomerPortal\Services\CustomerPortalService;
use App\Modules\Transactions\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class TransferController extends Controller
{
    public function __construct(
        protected CustomerPortalService $portalService,
        protected TransactionService $transactionService
    ) {}

    /**
     * Show transfer interface.
     */
    public function create(Request $request): View
    {
        $user = $request->user();
        $accounts = $this->portalService->getCustomerAccounts($user);
        $fromAccountId = $request->query('from_account_id', $accounts->first()?->id);

        return view('customer-portal::transfer', [
            'accounts' => $accounts,
            'selectedFromAccountId' => $fromAccountId,
        ]);
    }

    /**
     * Process transfer between accounts.
     */
    public function store(TransferRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        try {
            // Source account verification
            $fromAccount = $this->portalService->getCustomerAccount($user, (int) $validated['from_account_id']);
            $amount = (float) $validated['amount'];

            // Balance validation
            if ($fromAccount->balance < $amount) {
                return back()
                    ->withInput()
                    ->with('error', __('Insufficient balance. You currently have :bal :curr available.', [
                        'bal' => number_format((float) $fromAccount->balance, 2),
                        'curr' => $fromAccount->currency,
                    ]));
            }

            // Destination account determination
            $toAccount = null;
            $recipientName = null;

            if ($validated['transfer_mode'] === 'own_account') {
                $toAccount = $this->portalService->getCustomerAccount($user, (int) $validated['to_account_id']);
                $recipientName = __('Your Account (:num)', ['num' => $toAccount->account_number]);
            } else {
                $identifier = trim($validated['recipient_identifier']);
                $toAccount = Account::where('account_number', $identifier)
                    ->orWhere('iban', $identifier)
                    ->first();

                if (!$toAccount) {
                    return back()
                        ->withInput()
                        ->with('error', __('Recipient account with number or IBAN ":id" was not found in our banking registry.', ['id' => $identifier]));
                }

                if ($toAccount->id === $fromAccount->id) {
                    return back()
                        ->withInput()
                        ->with('error', __('Cannot transfer to the same source account.'));
                }

                $recipientName = $validated['recipient_name'] ?: ($toAccount->customer?->full_name ?? __('Registered Client'));
            }

            $desc = $validated['description'] ?: __('Transfer to :name', ['name' => $recipientName]);

            $result = $this->transactionService->transfer($fromAccount, $toAccount, $amount, [
                'transfer_mode' => $validated['transfer_mode'],
                'recipient_name' => $recipientName,
                'channel' => 'Customer Portal',
                'custom_description' => $desc,
            ]);

            $debitTxn = $result['debit'] ?? null;

            return redirect()->route('portal.dashboard')
                ->with('success', __('Successfully transferred :amount :currency to :recipient.', [
                    'amount' => number_format($amount, 2),
                    'currency' => $fromAccount->currency,
                    'recipient' => $recipientName,
                ]))
                ->with('transaction_reference', $debitTxn?->transaction_reference);

        } catch (Throwable $e) {
            return back()
                ->withInput()
                ->with('error', __('Transfer failed: :message', ['message' => $e->getMessage()]));
        }
    }
}
