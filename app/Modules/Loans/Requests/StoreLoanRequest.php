<?php

namespace App\Modules\Loans\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', Rule::exists('customers', 'id')],
            'account_id' => ['required', 'integer', Rule::exists('accounts', 'id')],
            'loan_type_id' => ['required', 'integer', Rule::exists('loan_types', 'id')],
            'requested_amount' => ['required', 'numeric', 'min:0.01', 'max:999999999999.99'],
            'term_months' => ['required', 'integer', 'min:1', 'max:600'],
            'purpose' => ['nullable', 'string', 'max:500'],
        ];
    }
}
