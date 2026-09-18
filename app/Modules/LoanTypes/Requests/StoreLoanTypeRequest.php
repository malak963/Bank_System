<?php

namespace App\Modules\LoanTypes\Requests;

use App\Modules\LoanTypes\Enums\LoanTypeStatus;
use App\Modules\Loans\Enums\InterestMethod;
use App\Modules\Loans\Enums\RepaymentFrequency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLoanTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:24', 'regex:/^[A-Z0-9_-]+$/', Rule::unique('loan_types', 'code')],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'currency' => ['required', 'alpha', 'size:3'],
            'minimum_amount' => ['required', 'numeric', 'min:0.01', 'max:999999999999.99'],
            'maximum_amount' => ['required', 'numeric', 'gt:minimum_amount', 'max:999999999999.99'],
            'minimum_term_months' => ['required', 'integer', 'min:1', 'max:600'],
            'maximum_term_months' => ['required', 'integer', 'gte:minimum_term_months', 'max:600'],
            'annual_interest_rate' => ['required', 'numeric', 'min:0', 'max:99.99'],
            'interest_method' => ['required', Rule::enum(InterestMethod::class)],
            'repayment_frequency' => ['required', Rule::enum(RepaymentFrequency::class)],
            'requires_collateral' => ['nullable', 'boolean'],
            'status' => ['required', Rule::enum(LoanTypeStatus::class)],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => $this->filled('code') ? strtoupper(trim((string) $this->input('code'))) : $this->input('code'),
            'currency' => $this->filled('currency') ? strtoupper(trim((string) $this->input('currency'))) : $this->input('currency'),
            'requires_collateral' => $this->boolean('requires_collateral'),
        ]);
    }
}
