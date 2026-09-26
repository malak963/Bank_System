<?php

namespace App\Modules\CustomerPortal\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayCustomInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'account_id' => ['required', 'integer', 'exists:accounts,id'],
            'bill_type' => ['required', 'string', 'in:electricity,water,gas,internet,phone,television,insurance,tax,loan_installment,subscription,other'],
            'provider_name' => ['required', 'string', 'max:100'],
            'bill_reference' => ['required', 'string', 'max:40'],
            'provider_account_number' => ['nullable', 'string', 'max:50'],
            'amount' => ['required', 'numeric', 'min:0.5', 'max:200000'],
            'due_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'account_id.required' => __('Please select the payment account.'),
            'provider_name.required' => __('Please specify the biller or provider name.'),
            'bill_reference.required' => __('Please enter the invoice or reference number.'),
            'amount.required' => __('Please enter the invoice amount.'),
            'amount.min' => __('Minimum invoice amount is 0.50.'),
        ];
    }
}
