<?php

namespace App\Modules\Transfers\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Transfers\Services\TransferService;
use App\Modules\Transfers\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TransferController extends Controller
{
    private TransferService $transferService;

    public function __construct(TransferService $transferService)
    {
        $this->transferService = $transferService;
    }

    public function index(): View
    {
        $transfers = Transfer::with(['fromAccount', 'toAccount', 'customer'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $statistics = $this->transferService->getTransferStatistics();

        return view('transfers::index', compact('transfers', 'statistics'));
    }

    public function create(): View
    {
        $transferTypes = \App\Modules\Transfers\Enums\TransferType::cases();
        $accounts = \App\Modules\Accounts\Models\Account::where('status', 'open')->get();
        $customers = \App\Modules\Customers\Models\Customer::all();

        return view('transfers::create', compact('transferTypes', 'accounts', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id' => 'nullable|exists:accounts,id',
            'customer_id' => 'required|exists:customers,id',
            'transfer_type' => 'required|string',
            'amount' => 'required|numeric|min:0.01|max:1000000',
            'description' => 'nullable|string|max:500',
            'recipient_name' => 'nullable|string|max:200',
            'recipient_account' => 'nullable|string|max:50',
            'recipient_bank' => 'nullable|string|max:200',
            'recipient_bank_address' => 'nullable|string',
            'swift_code' => 'nullable|string|max:11',
            'iban' => 'nullable|string|max:34',
            'routing_number' => 'nullable|string|max:9',
            'reference' => 'nullable|string|max:50',
            'currency' => 'nullable|string|max:3',
            'exchange_rate' => 'nullable|numeric|min:0',
            'converted_amount' => 'nullable|numeric|min:0',
            'fees' => 'nullable|numeric|min:0',
            'scheduled_for' => 'nullable|date',
        ]);

        try {
            $transfer = $this->transferService->createTransfer($validated);
            return redirect()
                ->route('transfers.show', $transfer)
                ->with('success', 'Transfer created successfully');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Transfer creation failed: ' . $e->getMessage());
        }
    }

    public function show(Transfer $transfer): View
    {
        $transfer->load(['fromAccount', 'toAccount', 'customer', 'transactions']);
        return view('transfers::show', compact('transfer'));
    }

    public function cancel(Request $request, Transfer $transfer)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $transfer = $this->transferService->cancelTransfer($transfer, $validated['reason']);
            return redirect()
                ->route('transfers.show', $transfer)
                ->with('success', 'Transfer cancelled successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Cancellation failed: ' . $e->getMessage());
        }
    }

    public function retry(Transfer $transfer)
    {
        try {
            $success = $this->transferService->retryTransfer($transfer);
            return redirect()
                ->route('transfers.show', $transfer)
                ->with('success', $success ? 'Transfer retried successfully' : 'Transfer retry failed');
        } catch (\Exception $e) {
            return back()->with('error', 'Retry failed: ' . $e->getMessage());
        }
    }

    public function processScheduled()
    {
        try {
            $count = $this->transferService->processScheduledTransfers();
            return back()->with('success', "{$count} scheduled transfers processed");
        } catch (\Exception $e) {
            return back()->with('error', 'Processing failed: ' . $e->getMessage());
        }
    }

    // API Methods
    public function apiIndex(Request $request): JsonResponse
    {
        $filters = $request->only(['customer_id', 'account_id', 'transfer_type', 'status', 'scheduled_only']);
        $transfers = $this->transferService->getTransfers($filters);
        
        return response()->json($transfers);
    }

    public function apiShow(Transfer $transfer): JsonResponse
    {
        $transfer->load(['fromAccount', 'toAccount', 'customer', 'transactions']);
        return response()->json($transfer);
    }

    public function apiStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id' => 'nullable|exists:accounts,id',
            'customer_id' => 'required|exists:customers,id',
            'transfer_type' => 'required|string',
            'amount' => 'required|numeric|min:0.01|max:1000000',
            'description' => 'nullable|string|max:500',
            'recipient_name' => 'nullable|string|max:200',
            'recipient_account' => 'nullable|string|max:50',
            'recipient_bank' => 'nullable|string|max:200',
            'swift_code' => 'nullable|string|max:11',
            'iban' => 'nullable|string|max:34',
            'routing_number' => 'nullable|string|max:9',
            'reference' => 'nullable|string|max:50',
            'currency' => 'nullable|string|max:3',
            'exchange_rate' => 'nullable|numeric|min:0',
            'converted_amount' => 'nullable|numeric|min:0',
            'fees' => 'nullable|numeric|min:0',
            'scheduled_for' => 'nullable|date',
        ]);

        try {
            $transfer = $this->transferService->createTransfer($validated);
            return response()->json($transfer, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function apiStatistics(): JsonResponse
    {
        $statistics = $this->transferService->getTransferStatistics();
        return response()->json($statistics);
    }
}
