<?php

namespace App\Modules\CustomerPortal\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\BillsPayments\Enums\BillStatus;
use App\Modules\BillsPayments\Enums\BillType;
use App\Modules\BillsPayments\Models\Bill;
use App\Modules\BillsPayments\Services\BillsPaymentService;
use App\Modules\CustomerPortal\Requests\PayCustomInvoiceRequest;
use App\Modules\CustomerPortal\Requests\PayInvoiceRequest;
use App\Modules\CustomerPortal\Services\CustomerPortalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class InvoiceController extends Controller
{
    public function __construct(
        protected CustomerPortalService $portalService,
        protected BillsPaymentService $billsPaymentService
    ) {}

    /**
     * List all customer bills and invoices with filter tabs.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $customer = $this->portalService->ensureCustomerProfile($user);
        $accounts = $this->portalService->getCustomerAccounts($user);

        $filter = $request->query('status', 'all');

        $query = Bill::where('customer_id', $customer->id)
            ->with(['account', 'transaction']);

        if ($filter === 'pending') {
            $query->whereIn('status', [BillStatus::PENDING->value, BillStatus::SCHEDULED->value]);
        } elseif ($filter === 'paid') {
            $query->where('status', BillStatus::COMPLETED->value);
        } elseif ($filter === 'overdue') {
            $query->where('due_date', '<', now()->toDateString())
                  ->where('status', '!=', BillStatus::COMPLETED->value);
        }

        $bills = $query->orderBy('due_date', 'asc')->paginate(12)->withQueryString();

        $stats = [
            'pending_count' => Bill::where('customer_id', $customer->id)->where('status', BillStatus::PENDING->value)->count(),
            'pending_sum' => (float) Bill::where('customer_id', $customer->id)->where('status', BillStatus::PENDING->value)->sum('amount'),
            'paid_count' => Bill::where('customer_id', $customer->id)->where('status', BillStatus::COMPLETED->value)->count(),
            'paid_sum' => (float) Bill::where('customer_id', $customer->id)->where('status', BillStatus::COMPLETED->value)->sum('amount'),
        ];

        return view('customer-portal::invoices', [
            'bills' => $bills,
            'accounts' => $accounts,
            'currentFilter' => $filter,
            'stats' => $stats,
            'billTypes' => BillType::cases(),
        ]);
    }

    /**
     * View invoice details.
     */
    public function show(Request $request, int $id): View
    {
        $user = $request->user();
        $customer = $this->portalService->ensureCustomerProfile($user);
        $accounts = $this->portalService->getCustomerAccounts($user);

        $bill = Bill::where('customer_id', $customer->id)
            ->with(['account', 'transaction'])
            ->findOrFail($id);

        return view('customer-portal::invoice-detail', [
            'bill' => $bill,
            'accounts' => $accounts,
        ]);
    }

    /**
     * Pay an existing pending invoice.
     */
    public function pay(PayInvoiceRequest $request, int $id): RedirectResponse
    {
        $user = $request->user();
        $customer = $this->portalService->ensureCustomerProfile($user);
        $validated = $request->validated();

        try {
            $bill = Bill::where('customer_id', $customer->id)->findOrFail($id);

            if ($bill->status === BillStatus::COMPLETED) {
                return back()->with('info', __('This invoice has already been settled.'));
            }

            $account = $this->portalService->getCustomerAccount($user, (int) $validated['account_id']);

            if ($account->balance < $bill->amount) {
                return back()->with('error', __('Insufficient funds in account :acc to pay invoice amount of :amt :curr.', [
                    'acc' => $account->account_number,
                    'amt' => number_format((float) $bill->amount, 2),
                    'curr' => $account->currency,
                ]));
            }

            $paidBill = $this->billsPaymentService->payBill($bill->id, $account->id);

            return redirect()->route('portal.invoices')
                ->with('success', __('Invoice :ref paid successfully using account :acc!', [
                    'ref' => $paidBill->bill_reference,
                    'acc' => $account->account_number,
                ]))
                ->with('transaction_reference', $paidBill->transaction?->transaction_reference);

        } catch (Throwable $e) {
            return back()->with('error', __('Invoice payment failed: :message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Create and settle a custom new invoice or utility bill immediately.
     */
    public function payCustom(PayCustomInvoiceRequest $request): RedirectResponse
    {
        $user = $request->user();
        $customer = $this->portalService->ensureCustomerProfile($user);
        $validated = $request->validated();

        try {
            $account = $this->portalService->getCustomerAccount($user, (int) $validated['account_id']);
            $amount = (float) $validated['amount'];

            if ($account->balance < $amount) {
                return back()
                    ->withInput()
                    ->with('error', __('Insufficient funds in account :acc. Available: :bal :curr.', [
                        'acc' => $account->account_number,
                        'bal' => number_format((float) $account->balance, 2),
                        'curr' => $account->currency,
                    ]));
            }

            // Create bill
            $bill = $this->billsPaymentService->createBill([
                'customer_id' => $customer->id,
                'account_id' => $account->id,
                'bill_type' => $validated['bill_type'],
                'provider_name' => $validated['provider_name'],
                'provider_account_number' => $validated['provider_account_number'] ?? null,
                'amount' => $amount,
                'currency' => $account->currency,
                'due_date' => $validated['due_date'] ?? now()->toDateString(),
                'description' => $validated['description'] ?? __('Direct custom invoice payment'),
            ]);

            // Now immediately pay it
            $paidBill = $this->billsPaymentService->payBill($bill->id, $account->id);

            return redirect()->route('portal.invoices')
                ->with('success', __('Invoice :ref (:provider) for :amt :curr was settled successfully!', [
                    'ref' => $paidBill->bill_reference,
                    'provider' => $paidBill->provider_name,
                    'amt' => number_format($amount, 2),
                    'curr' => $account->currency,
                ]))
                ->with('transaction_reference', $paidBill->transaction?->transaction_reference);

        } catch (Throwable $e) {
            return back()
                ->withInput()
                ->with('error', __('Failed to process invoice payment: :message', ['message' => $e->getMessage()]));
        }
    }
}
