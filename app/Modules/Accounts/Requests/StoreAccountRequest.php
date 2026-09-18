<?php

namespace App\Modules\Accounts\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', Rule::exists('customers', 'id')],
            'account_type_id' => ['required', 'integer', Rule::exists('account_types', 'id')],
            'initial_deposit' => ['nullable', 'numeric', 'min:0', 'max:999999999999.99'],
        ];
    }
}
