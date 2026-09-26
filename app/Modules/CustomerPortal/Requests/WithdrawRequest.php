<?php

namespace App\Modules\CustomerPortal\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawRequest extends FormRequest
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
            'method' => ['required', 'string', 'in:atm,branch_pickup,wire_transfer'],
            'description' => ['nullable', 'string', 'max:255'],
            'beneficiary_bank' => ['nullable', 'string', 'max:150'],
            'beneficiary_iban' => ['nullable', 'string', 'max:40'],
            'pickup_branch' => ['nullable', 'string', 'max:150'],
            'pin' => ['nullable', 'string', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'account_id.required' => __('Please select the source account to withdraw from.'),
            'amount.required' => __('Please enter an amount to withdraw.'),
            'amount.min' => __('Minimum withdrawal amount is 1.00.'),
            'method.required' => __('Please select a withdrawal method.'),
        ];
    }
}
