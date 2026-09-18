<?php

namespace App\Modules\BillsPayments\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\BillsPayments\Services\BillsPaymentService;
use Illuminate\Http\Request;

class BillsPaymentController extends Controller
{
    public function __construct(
        private BillsPaymentService $billsPaymentService
    ) {}

    public function index(Request $request)
    {
        $bills = $this->billsPaymentService->getAllBills($request->all());
        return view('bills-payments::index', compact('bills'));
    }

    public function create()
    {
        return view('bills-payments::create');
    }

    public function store(Request $request)
    {
        $bill = $this->billsPaymentService->createBill($request->all());
        return redirect()->route('bills-payments.show', $bill->id)
            ->with('success', 'Bill created successfully');
    }

    public function show($id)
    {
        $bill = $this->billsPaymentService->getBill($id);
        return view('bills-payments::show', compact('bill'));
    }

    public function edit($id)
    {
        $bill = $this->billsPaymentService->getBill($id);
        return view('bills-payments::edit', compact('bill'));
    }

    public function update(Request $request, $id)
    {
        $bill = $this->billsPaymentService->updateBill($id, $request->all());
        return redirect()->route('bills-payments.show', $bill->id)
            ->with('success', 'Bill updated successfully');
    }

    public function pay(Request $request, $id)
    {
        $bill = $this->billsPaymentService->payBill($id, $request->account_id);
        return redirect()->route('bills-payments.show', $bill->id)
            ->with('success', 'Bill paid successfully');
    }

    public function schedule(Request $request, $id)
    {
        $bill = $this->billsPaymentService->scheduleBill($id, $request->scheduled_date);
        return redirect()->route('bills-payments.show', $bill->id)
            ->with('success', 'Bill scheduled successfully');
    }

    public function cancel($id)
    {
        $bill = $this->billsPaymentService->cancelBill($id);
        return redirect()->route('bills-payments.show', $bill->id)
            ->with('success', 'Bill cancelled successfully');
    }

    public function refund(Request $request, $id)
    {
        $bill = $this->billsPaymentService->refundBill($id, $request->reason);
        return redirect()->route('bills-payments.show', $bill->id)
            ->with('success', 'Bill refunded successfully');
    }

    public function destroy($id)
    {
        $this->billsPaymentService->deleteBill($id);
        return redirect()->route('bills-payments.index')
            ->with('success', 'Bill deleted successfully');
    }

    public function overdue()
    {
        $bills = $this->billsPaymentService->getOverdueBills();
        return view('bills-payments::overdue', compact('bills'));
    }

    public function upcoming()
    {
        $bills = $this->billsPaymentService->getUpcomingBills();
        return view('bills-payments::upcoming', compact('bills'));
    }
}
