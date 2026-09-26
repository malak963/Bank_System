<?php

namespace App\Modules\CustomerPortal\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'account_id' => ['required', 'integer', 'exists:accounts,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'account_id.required' => __('Please select an account to pay the invoice from.'),
            'account_id.exists' => __('The selected payment account is invalid.'),
        ];
    }
}
