<?php

namespace App\Modules\Calculators\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Calculators\Services\LoanCalculatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoanCalculatorController extends Controller
{
    public function __construct(private LoanCalculatorService $calculator) {}

    public function index()
    {
        return view('calculators::index');
    }

    public function loan(Request $request)
    {
        $result = null;
        if ($request->filled(['amount', 'interest_rate', 'term_months'])) {
            try {
                $validated = $request->validate([
                    'amount' => 'required|numeric|min:1000|max:10000000',
                    'interest_rate' => 'required|numeric|min:0.1|max:30',
                    'term_months' => 'required|integer|min:1|max:360',
                    'method' => 'nullable|in:reducing_balance,flat_rate',
                    'frequency' => 'nullable|in:monthly,quarterly',
                ]);
                $result = $this->calculator->calculateLoan($validated);
            } catch (\Throwable $e) {
                // Ignore validation exception for optional GET query params
            }
        }

        return view('calculators::loan', compact('result'));
    }

    public function calculateLoan(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1000|max:10000000',
            'interest_rate' => 'required|numeric|min:0.1|max:30',
            'term_months' => 'required|integer|min:1|max:360',
            'method' => 'nullable|in:reducing_balance,flat_rate',
            'frequency' => 'nullable|in:monthly,quarterly',
        ]);

        $result = $this->calculator->calculateLoan($validated);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    public function affordability(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'monthly_income' => 'required|numeric|min:0',
            'monthly_expenses' => 'required|numeric|min:0',
            'existing_debts' => 'nullable|numeric|min:0',
            'interest_rate' => 'nullable|numeric|min:0.1|max:30',
            'term_months' => 'nullable|integer|min:1|max:360',
        ]);

        $result = $this->calculator->calculateAffordability($validated);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }
}
