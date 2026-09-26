<?php

namespace App\Modules\CustomerPortal\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CustomerPortal\Services\CustomerPortalService;
use App\Modules\Transactions\Enums\TransactionStatus;
use App\Modules\Transactions\Enums\TransactionType;
use App\Modules\Transactions\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionHistoryController extends Controller
{
    public function __construct(
        protected CustomerPortalService $portalService
    ) {}

    /**
     * Show filtered transaction history for customer accounts.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $accounts = $this->portalService->getCustomerAccounts($user);
        $accountIds = $accounts->pluck('id')->toArray();

        $query = Transaction::whereIn('account_id', $accountIds)
            ->with(['account.accountType']);

        // Account filter
        if ($request->filled('account_id')) {
            $query->where('account_id', $request->query('account_id'));
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('transaction_type', $request->query('type'));
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        // Date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->query('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->query('date_to'));
        }

        // Search in reference / description
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('transaction_reference', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        $transactions = $query->latest('created_at')->paginate(15)->withQueryString();

        return view('customer-portal::transactions', [
            'transactions' => $transactions,
            'accounts' => $accounts,
            'types' => TransactionType::cases(),
            'statuses' => TransactionStatus::cases(),
        ]);
    }

    /**
     * View digital transaction receipt.
     */
    public function receipt(Request $request, int $id): View
    {
        $user = $request->user();
        $accounts = $this->portalService->getCustomerAccounts($user);
        $accountIds = $accounts->pluck('id')->toArray();

        $transaction = Transaction::whereIn('account_id', $accountIds)
            ->with(['account.customer', 'account.branch'])
            ->findOrFail($id);

        return view('customer-portal::receipt', [
            'transaction' => $transaction,
        ]);
    }
}
