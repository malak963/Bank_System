<?php

namespace App\Modules\Transactions\Requests;

use App\Modules\Transactions\Enums\TransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_id' => ['required', 'exists:accounts,id'],
            'transaction_type' => ['required', Rule::enum(TransactionType::class)],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:10000000'],
            'description' => ['nullable', 'string', 'max:500'],
            'reference_number' => ['nullable', 'string', 'max:50'],
            'category' => ['nullable', 'string', 'max:100'],
            'sub_category' => ['nullable', 'string', 'max:100'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'metadata' => ['nullable', 'array'],
            'related_transaction_id' => ['nullable', 'exists:transactions,id'],
            'fees' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'account_id.required' => 'The account field is required.',
            'account_id.exists' => 'The selected account is invalid.',
            'transaction_type.required' => 'The transaction type field is required.',
            'transaction_type.enum' => 'The selected transaction type is invalid.',
            'amount.required' => 'The amount field is required.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.min' => 'The amount must be at least 0.01.',
            'amount.max' => 'The amount cannot exceed 10,000,000.',
        ];
    }
}
