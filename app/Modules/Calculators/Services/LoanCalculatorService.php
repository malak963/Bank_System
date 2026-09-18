<?php

namespace App\Modules\Calculators\Services;

class LoanCalculatorService
{
    public function calculateLoan(array $data): array
    {
        $principal = (float) $data['amount'];
        $annualRate = (float) $data['interest_rate'];
        $termMonths = (int) $data['term_months'];
        $method = $data['method'] ?? 'reducing_balance'; // 'reducing_balance' or 'flat_rate'
        $frequency = $data['frequency'] ?? 'monthly'; // 'monthly' or 'quarterly'

        $monthlyRate = $annualRate / 100 / 12;
        
        if ($method === 'reducing_balance') {
            return $this->calculateReducingBalance($principal, $monthlyRate, $termMonths, $frequency);
        } else {
            return $this->calculateFlatRate($principal, $annualRate, $termMonths, $frequency);
        }
    }

    private function calculateReducingBalance(float $principal, float $monthlyRate, int $termMonths, string $frequency): array
    {
        $paymentsPerYear = $frequency === 'quarterly' ? 4 : 12;
        $ratePerPeriod = $monthlyRate * ($frequency === 'quarterly' ? 3 : 1);
        $totalPayments = $frequency === 'quarterly' ? ceil($termMonths / 3) : $termMonths;

        // EMI formula: P * r * (1+r)^n / ((1+r)^n - 1)
        $emi = $principal * $ratePerPeriod * pow(1 + $ratePerPeriod, $totalPayments) / 
               (pow(1 + $ratePerPeriod, $totalPayments) - 1);

        $totalAmount = $emi * $totalPayments;
        $totalInterest = $totalAmount - $principal;

        $schedule = $this->generateAmortizationSchedule($principal, $ratePerPeriod, $emi, $totalPayments, $frequency);

        return [
            'method' => 'reducing_balance',
            'frequency' => $frequency,
            'principal' => $principal,
            'annualInterestRate' => $monthlyRate * 12 * 100,
            'termMonths' => $termMonths,
            'monthlyPayment' => $emi,
            'totalAmount' => $totalAmount,
            'totalInterest' => $totalInterest,
            'effectiveRate' => ($totalInterest / $principal) * 100,
            'schedule' => $schedule,
        ];
    }

    private function calculateFlatRate(float $principal, float $annualRate, int $termMonths, string $frequency): array
    {
        $paymentsPerYear = $frequency === 'quarterly' ? 4 : 12;
        $totalPayments = $frequency === 'quarterly' ? ceil($termMonths / 3) : $termMonths;

        $totalInterest = $principal * ($annualRate / 100) * ($termMonths / 12);
        $totalAmount = $principal + $totalInterest;
        $emi = $totalAmount / $totalPayments;

        $schedule = $this->generateFlatRateSchedule($principal, $totalInterest, $emi, $totalPayments, $frequency);

        return [
            'method' => 'flat_rate',
            'frequency' => $frequency,
            'principal' => $principal,
            'annualInterestRate' => $annualRate,
            'termMonths' => $termMonths,
            'monthlyPayment' => $emi,
            'totalAmount' => $totalAmount,
            'totalInterest' => $totalInterest,
            'effectiveRate' => ($totalInterest / $principal) * 100,
            'schedule' => $schedule,
        ];
    }

    private function generateAmortizationSchedule(float $principal, float $rate, float $emi, int $totalPayments, string $frequency): array
    {
        $schedule = [];
        $balance = $principal;

        for ($i = 1; $i <= $totalPayments; $i++) {
            $interest = $balance * $rate;
            $principalPayment = $emi - $interest;
            $balance -= $principalPayment;

            $schedule[] = [
                'paymentNumber' => $i,
                'paymentAmount' => $emi,
                'principalComponent' => $principalPayment,
                'interestComponent' => $interest,
                'remainingBalance' => max(0, $balance),
            ];
        }

        return $schedule;
    }

    private function generateFlatRateSchedule(float $principal, float $totalInterest, float $emi, int $totalPayments, string $frequency): array
    {
        $schedule = [];
        $balance = $principal;
        $interestPerPayment = $totalInterest / $totalPayments;
        $principalPerPayment = $principal / $totalPayments;

        for ($i = 1; $i <= $totalPayments; $i++) {
            $balance -= $principalPerPayment;

            $schedule[] = [
                'paymentNumber' => $i,
                'paymentAmount' => $emi,
                'principalComponent' => $principalPerPayment,
                'interestComponent' => $interestPerPayment,
                'remainingBalance' => max(0, $balance),
            ];
        }

        return $schedule;
    }

    public function calculateAffordability(array $data): array
    {
        $monthlyIncome = (float) $data['monthly_income'];
        $monthlyExpenses = (float) $data['monthly_expenses'];
        $existingDebts = (float) $data['existing_debts'] ?? 0;
        $interestRate = (float) $data['interest_rate'] ?? 10;
        $termMonths = (int) $data['term_months'] ?? 60;

        $disposableIncome = $monthlyIncome - $monthlyExpenses - $existingDebts;
        $dtiRatio = ($existingDebts / $monthlyIncome) * 100;

        // Conservative approach: max 40% DTI ratio
        $maxMonthlyPayment = $monthlyIncome * 0.4 - $existingDebts;
        
        if ($maxMonthlyPayment <= 0) {
            return [
                'affordable' => false,
                'reason' => 'Debt-to-income ratio too high',
                'maxLoanAmount' => 0,
                'recommendedAmount' => 0,
            ];
        }

        $monthlyRate = $interestRate / 100 / 12;
        $maxLoanAmount = $maxMonthlyPayment * (1 - pow(1 + $monthlyRate, -$termMonths)) / $monthlyRate;

        return [
            'affordable' => true,
            'disposableIncome' => $disposableIncome,
            'dtiRatio' => $dtiRatio,
            'maxMonthlyPayment' => $maxMonthlyPayment,
            'maxLoanAmount' => $maxLoanAmount,
            'recommendedAmount' => $maxLoanAmount * 0.8, // 80% of max for safety margin
            'affordabilityScore' => $this->calculateAffordabilityScore($dtiRatio, $disposableIncome / $monthlyIncome),
        ];
    }

    private function calculateAffordabilityScore(float $dtiRatio, float $disposableRatio): int
    {
        // Score out of 100
        $dtiScore = max(0, 100 - ($dtiRatio * 2)); // Lower DTI is better
        $disposableScore = $disposableRatio * 100; // Higher disposable ratio is better
        
        return (int) round(($dtiScore + $disposableScore) / 2);
    }
}
