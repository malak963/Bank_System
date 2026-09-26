<?php

namespace App\Modules\CustomerPortal\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'transfer_mode' => ['required', 'string', 'in:own_account,other_account'],
            'from_account_id' => ['required', 'integer', 'exists:accounts,id'],
            'to_account_id' => ['nullable', 'required_if:transfer_mode,own_account', 'different:from_account_id', 'exists:accounts,id'],
            'recipient_identifier' => ['nullable', 'required_if:transfer_mode,other_account', 'string', 'max:50'],
            'recipient_name' => ['nullable', 'string', 'max:150'],
            'amount' => ['required', 'numeric', 'min:1', 'max:500000'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'from_account_id.required' => __('Please select the source account.'),
            'to_account_id.required_if' => __('Please select a destination account for internal transfer.'),
            'to_account_id.different' => __('Source and destination accounts must be different.'),
            'recipient_identifier.required_if' => __('Please provide the recipient account number or IBAN.'),
            'amount.required' => __('Please specify the transfer amount.'),
            'amount.min' => __('Minimum transfer amount is 1.00.'),
        ];
    }
}
