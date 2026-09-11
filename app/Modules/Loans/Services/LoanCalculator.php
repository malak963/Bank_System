<?php

namespace App\Modules\Loans\Services;

use App\Modules\Loans\Enums\InterestMethod;
use App\Modules\Loans\Enums\RepaymentFrequency;
use Carbon\CarbonInterface;
use Illuminate\Validation\ValidationException;

class LoanCalculator
{
    /**
     * Build a fully rounded amortization schedule from the approved loan snapshot.
     * Amounts are rounded per installment and the final installment absorbs rounding residue.
     */
    public function schedule(
        float $principal,
        float $annualRate,
        int $termMonths,
        InterestMethod $interestMethod,
        RepaymentFrequency $frequency,
        CarbonInterface $firstPaymentDate,
    ): array {
        $monthsPerPeriod = $frequency->monthsPerPeriod();

        if ($termMonths < 1 || $termMonths % $monthsPerPeriod !== 0) {
            throw ValidationException::withMessages([
                'term_months' => 'The term must be divisible by the selected repayment frequency.',
            ]);
        }

        $periods = intdiv($termMonths, $monthsPerPeriod);
        $periodicRate = $annualRate / 100 / $frequency->periodsPerYear();
        $remainingPrincipal = round($principal, 2);
        $regularPayment = $this->regularPayment($remainingPrincipal, $periodicRate, $periods, $interestMethod);
        $flatInterestPerPeriod = $interestMethod === InterestMethod::FlatRate
            ? round(($principal * $annualRate / 100 * ($termMonths / 12)) / $periods, 2)
            : 0.0;
        $schedule = [];
        $totalInterest = 0.0;

        for ($period = 1; $period <= $periods; $period++) {
            $interest = $interestMethod === InterestMethod::FlatRate
                ? $flatInterestPerPeriod
                : round($remainingPrincipal * $periodicRate, 2);
            $principalPart = $interestMethod === InterestMethod::FlatRate
                ? round($principal / $periods, 2)
                : round($regularPayment - $interest, 2);

            if ($period === $periods) {
                $principalPart = round($remainingPrincipal, 2);
                if ($interestMethod === InterestMethod::FlatRate) {
                    $interest = round(($principal * $annualRate / 100 * ($termMonths / 12)) - $totalInterest, 2);
                }
            }

            $principalPart = max(0, min($remainingPrincipal, $principalPart));
            $amountDue = round($principalPart + $interest, 2);
            $totalInterest = round($totalInterest + $interest, 2);
            $schedule[] = [
                'installment_number' => $period,
                'due_date' => $firstPaymentDate->copy()->addMonths($monthsPerPeriod * ($period - 1))->toDateString(),
                'principal_amount' => $principalPart,
                'interest_amount' => $interest,
                'amount_due' => $amountDue,
            ];
            $remainingPrincipal = round($remainingPrincipal - $principalPart, 2);
        }

        return [
            'installments' => $schedule,
            'total_interest' => $totalInterest,
            'total_payable' => round($principal + $totalInterest, 2),
        ];
    }

    private function regularPayment(float $principal, float $periodicRate, int $periods, InterestMethod $method): float
    {
        if ($method === InterestMethod::FlatRate || $periodicRate == 0.0) {
            return round($principal / $periods, 2);
        }

        $factor = pow(1 + $periodicRate, $periods);

        return round($principal * $periodicRate * $factor / ($factor - 1), 2);
    }
}
