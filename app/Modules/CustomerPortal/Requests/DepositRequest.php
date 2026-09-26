<?php

namespace App\Modules\CustomerPortal\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'account_id' => ['required', 'integer', 'exists:accounts,id'],
            'amount' => ['required', 'numeric', 'min:1', 'max:500000'],
            'method' => ['required', 'string', 'in:card,bank_wire,cash_pickup,online_gateway'],
            'description' => ['nullable', 'string', 'max:255'],
            'card_number' => ['nullable', 'string', 'max:24'],
            'card_holder' => ['nullable', 'string', 'max:100'],
            'card_expiry' => ['nullable', 'string', 'max:7'],
            'reference_note' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'account_id.required' => __('Please select a destination account.'),
            'account_id.exists' => __('The selected account does not exist.'),
            'amount.required' => __('Please specify a deposit amount.'),
            'amount.min' => __('Minimum deposit amount is 1.00.'),
            'amount.max' => __('Maximum single deposit limit is 500,000.00.'),
            'method.required' => __('Please choose a payment method.'),
        ];
    }
}
