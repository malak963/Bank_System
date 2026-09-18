<?php

namespace App\Modules\Transactions\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounts\Models\Account;
use App\Modules\Transactions\Requests\CreateTransactionRequest;
use App\Modules\Transactions\Services\TransactionService;
use App\Modules\Transactions\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TransactionController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(): View
    {
        $transactions = Transaction::with(['account', 'customer', 'branch'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('transactions::index', compact('transactions'));
    }

    public function create(): View
    {
        $accounts = Account::where('status', 'open')->get();
        return view('transactions::create', compact('accounts'));
    }

    public function store(CreateTransactionRequest $request)
    {
        try {
            $transaction = $this->transactionService->createTransaction($request->validated());
            
            return redirect()
                ->route('transactions.show', $transaction)
                ->with('success', 'Transaction created successfully');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Transaction failed: ' . $e->getMessage());
        }
    }

    public function show(Transaction $transaction): View
    {
        $transaction->load(['account', 'customer', 'branch', 'relatedTransaction', 'createdBy', 'reversedBy']);
        return view('transactions::show', compact('transaction'));
    }

    public function edit(Transaction $transaction): View
    {
        // Transactions typically shouldn't be editable, but can update metadata
        return view('transactions::edit', compact('transaction'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'description' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:100',
            'sub_category' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'metadata' => 'nullable|array',
        ]);

        $transaction->update($validated);

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('success', 'Transaction updated successfully');
    }

    public function destroy(Transaction $transaction)
    {
        if (!$transaction->status->canBeCancelled()) {
            return back()->with('error', 'This transaction cannot be cancelled');
        }

        $transaction->update(['status' => TransactionStatus::Cancelled]);

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaction cancelled successfully');
    }

    public function reverse(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $reversal = $this->transactionService->reverseTransaction(
                $transaction,
                $validated['reason']
            );

            return redirect()
                ->route('transactions.show', $reversal)
                ->with('success', 'Transaction reversed successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Reversal failed: ' . $e->getMessage());
        }
    }

    // API Methods
    public function apiIndex(Request $request): JsonResponse
    {
        $query = Transaction::with(['account', 'customer', 'branch']);

        if ($request->has('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->has('type')) {
            $query->where('transaction_type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('from_date')) {
            $query->where('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 20));

        return response()->json($transactions);
    }

    public function apiStore(CreateTransactionRequest $request): JsonResponse
    {
        try {
            $transaction = $this->transactionService->createTransaction($request->validated());
            return response()->json($transaction, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function apiShow(Transaction $transaction): JsonResponse
    {
        $transaction->load(['account', 'customer', 'branch', 'relatedTransaction', 'createdBy', 'reversedBy']);
        return response()->json($transaction);
    }

    public function apiByAccount(Account $account, Request $request): JsonResponse
    {
        $filters = $request->only(['type', 'status', 'from_date', 'to_date', 'min_amount', 'max_amount']);
        $transactions = $this->transactionService->getTransactionHistory($account, $filters);
        
        return response()->json($transactions);
    }

    public function apiReverse(Request $request, Transaction $transaction): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $reversal = $this->transactionService->reverseTransaction(
                $transaction,
                $validated['reason'],
                $request->user()->id ?? null
            );
            return response()->json($reversal, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
