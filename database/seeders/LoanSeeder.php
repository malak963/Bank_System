<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Accounts\Models\Account;
use App\Modules\Branches\Models\Branch;
use App\Modules\Customers\Models\Customer;
use App\Modules\Installments\Models\Installment;
use App\Modules\Loans\Enums\InterestMethod;
use App\Modules\Loans\Enums\LoanStatus;
use App\Modules\Loans\Enums\RepaymentFrequency;
use App\Modules\Loans\Models\Loan;
use App\Modules\Loans\Models\LoanPayment;
use App\Modules\Loans\Models\LoanPaymentAllocation;
use App\Modules\LoanTypes\Models\LoanType;
use Illuminate\Database\Seeder;

class LoanSeeder extends Seeder
{
    public function run(): void
    {
        $officer = User::where('email', 'loan_officer@bank.com')->first();
        $officerId = $officer?->id;
        $branch = Branch::where('code', 'BR-DAM-01')->first() ?? Branch::first();

        $personalType = LoanType::where('code', 'PERSONAL')->first();
        $vehicleType = LoanType::where('code', 'VEHICLE')->first() ?? $personalType;
        $businessType = LoanType::where('code', 'BUSINESS')->first() ?? $personalType;

        $ahmad = Customer::where('customer_number', 'CUST-000001')->first();
        $layla = Customer::where('customer_number', 'CUST-000002')->first();
        $omar = Customer::where('customer_number', 'CUST-000003')->first();

        $ahmadAcc = Account::where('account_number', '1000100001')->first();
        $laylaAcc = Account::where('account_number', '1000200001')->first();
        $omarAcc = Account::where('account_number', '1000300002')->first();

        // 1. Personal Loan for Ahmad (Active, partially paid)
        if ($ahmad && $personalType && $ahmadAcc) {
            $loan1 = Loan::updateOrCreate(
                ['loan_reference' => 'LN-2025-0001'],
                [
                    'branch_id' => $branch->id,
                    'customer_id' => $ahmad->id,
                    'account_id' => $ahmadAcc->id,
                    'loan_type_id' => $personalType->id,
                    'requested_amount' => 12000000.00,
                    'approved_amount' => 12000000.00,
                    'disbursed_amount' => 12000000.00,
                    'annual_interest_rate' => 12.00,
                    'term_months' => 12,
                    'interest_method' => InterestMethod::ReducingBalance,
                    'repayment_frequency' => RepaymentFrequency::Monthly,
                    'purpose' => 'تجديد وتحسين المنزل - Home Renovation Financing',
                    'status' => LoanStatus::Active,
                    'application_date' => now()->subMonths(6)->format('Y-m-d'),
                    'approved_at' => now()->subMonths(6),
                    'approved_by' => $officerId,
                    'disbursed_at' => now()->subMonths(6),
                    'first_payment_date' => now()->subMonths(5)->format('Y-m-d'),
                    'total_interest' => 780000.00,
                    'total_payable' => 12780000.00,
                    'total_paid' => 5325000.00,
                    'outstanding_principal' => 7000000.00,
                    'outstanding_interest' => 455000.00,
                    'next_payment_date' => now()->addDays(15)->format('Y-m-d'),
                ]
            );

            // Generate 12 Installments
            $monthlyPrincipal = 1000000.00;
            $monthlyInterest = 65000.00;
            $monthlyDue = 1065000.00;

            for ($i = 1; $i <= 12; $i++) {
                $dueDate = now()->subMonths(6)->addMonths($i);
                $isPaid = $i <= 5;

                $inst = Installment::updateOrCreate(
                    [
                        'loan_id' => $loan1->id,
                        'installment_number' => $i,
                    ],
                    [
                        'due_date' => $dueDate->format('Y-m-d'),
                        'principal_amount' => $monthlyPrincipal,
                        'interest_amount' => $monthlyInterest,
                        'amount_due' => $monthlyDue,
                        'principal_paid' => $isPaid ? $monthlyPrincipal : 0.00,
                        'interest_paid' => $isPaid ? $monthlyInterest : 0.00,
                        'amount_paid' => $isPaid ? $monthlyDue : 0.00,
                        'status' => $isPaid ? 'paid' : 'pending',
                        'paid_at' => $isPaid ? $dueDate->copy()->subDays(2) : null,
                        'last_payment_at' => $isPaid ? $dueDate->copy()->subDays(2) : null,
                    ]
                );

                if ($isPaid && !LoanPaymentAllocation::where('installment_id', $inst->id)->exists()) {
                    $payment = LoanPayment::updateOrCreate(
                        ['payment_reference' => 'PAY-LN1-' . str_pad($i, 3, '0', STR_PAD_LEFT)],
                        [
                            'loan_id' => $loan1->id,
                            'amount' => $monthlyDue,
                            'principal_amount' => $monthlyPrincipal,
                            'interest_amount' => $monthlyInterest,
                            'payment_method' => 'account_debit',
                            'paid_at' => $dueDate->copy()->subDays(2),
                            'received_by' => $officerId,
                            'notes' => 'سداد القسط رقم ' . $i,
                        ]
                    );

                    LoanPaymentAllocation::updateOrCreate(
                        [
                            'loan_payment_id' => $payment->id,
                            'installment_id' => $inst->id,
                        ],
                        [
                            'amount' => $monthlyDue,
                            'principal_amount' => $monthlyPrincipal,
                            'interest_amount' => $monthlyInterest,
                        ]
                    );
                }
            }
        }

        // 2. Vehicle Loan for Layla (Active)
        if ($layla && $vehicleType && $laylaAcc) {
            $loan2 = Loan::updateOrCreate(
                ['loan_reference' => 'LN-2025-0002'],
                [
                    'branch_id' => $branch->id,
                    'customer_id' => $layla->id,
                    'account_id' => $laylaAcc->id,
                    'loan_type_id' => $vehicleType->id,
                    'requested_amount' => 36000000.00,
                    'approved_amount' => 36000000.00,
                    'disbursed_amount' => 36000000.00,
                    'annual_interest_rate' => 11.25,
                    'term_months' => 24,
                    'interest_method' => InterestMethod::ReducingBalance,
                    'repayment_frequency' => RepaymentFrequency::Monthly,
                    'purpose' => 'تمويل سيارة خاصة جديدة - New Private Vehicle Purchase',
                    'status' => LoanStatus::Active,
                    'application_date' => now()->subMonths(3)->format('Y-m-d'),
                    'approved_at' => now()->subMonths(3),
                    'approved_by' => $officerId,
                    'disbursed_at' => now()->subMonths(3),
                    'first_payment_date' => now()->subMonths(2)->format('Y-m-d'),
                    'total_interest' => 4320000.00,
                    'total_payable' => 40320000.00,
                    'total_paid' => 3360000.00,
                    'outstanding_principal' => 33000000.00,
                    'outstanding_interest' => 3960000.00,
                    'next_payment_date' => now()->addDays(20)->format('Y-m-d'),
                ]
            );

            // Generate Installments
            for ($i = 1; $i <= 24; $i++) {
                $dueDate = now()->subMonths(3)->addMonths($i);
                $isPaid = $i <= 2;

                Installment::updateOrCreate(
                    [
                        'loan_id' => $loan2->id,
                        'installment_number' => $i,
                    ],
                    [
                        'due_date' => $dueDate->format('Y-m-d'),
                        'principal_amount' => 1500000.00,
                        'interest_amount' => 180000.00,
                        'amount_due' => 1680000.00,
                        'principal_paid' => $isPaid ? 1500000.00 : 0.00,
                        'interest_paid' => $isPaid ? 180000.00 : 0.00,
                        'amount_paid' => $isPaid ? 1680000.00 : 0.00,
                        'status' => $isPaid ? 'paid' : 'pending',
                        'paid_at' => $isPaid ? $dueDate->copy()->subDays(1) : null,
                        'last_payment_at' => $isPaid ? $dueDate->copy()->subDays(1) : null,
                    ]
                );
            }
        }

        // 3. Commercial Loan for Omar (Pending Review)
        if ($omar && $businessType && $omarAcc) {
            Loan::updateOrCreate(
                ['loan_reference' => 'LN-2026-0003'],
                [
                    'branch_id' => $branch->id,
                    'customer_id' => $omar->id,
                    'account_id' => $omarAcc->id,
                    'loan_type_id' => $businessType->id,
                    'requested_amount' => 150000000.00,
                    'approved_amount' => null,
                    'disbursed_amount' => null,
                    'annual_interest_rate' => 10.50,
                    'term_months' => 36,
                    'interest_method' => InterestMethod::ReducingBalance,
                    'repayment_frequency' => RepaymentFrequency::Monthly,
                    'purpose' => 'توسعة خط إنتاج وتصدير - Commercial Factory Production Line Expansion',
                    'status' => LoanStatus::Pending,
                    'application_date' => now()->subDays(5)->format('Y-m-d'),
                    'total_interest' => 24000000.00,
                    'total_payable' => 174000000.00,
                    'total_paid' => 0.00,
                    'outstanding_principal' => 150000000.00,
                    'outstanding_interest' => 24000000.00,
                ]
            );
        }
    }
}
