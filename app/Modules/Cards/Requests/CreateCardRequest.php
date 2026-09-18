<?php

namespace App\Modules\Cards\Requests;

use App\Modules\Cards\Enums\CardBrand;
use App\Modules\Cards\Enums\CardType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_id' => ['required', 'exists:accounts,id'],
            'card_type' => ['required', Rule::enum(CardType::class)],
            'card_brand' => ['required', Rule::enum(CardBrand::class)],
            'card_holder_name' => ['required', 'string', 'max:100'],
            'daily_limit' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'monthly_limit' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'international_enabled' => ['nullable', 'boolean'],
            'online_enabled' => ['nullable', 'boolean'],
            'contactless_enabled' => ['nullable', 'boolean'],
            'delivery_method' => ['nullable', 'string', 'in:branch,mail,courier'],
            'priority' => ['nullable', 'string', 'in:standard,express,urgent'],
        ];
    }

    public function messages(): array
    {
        return [
            'account_id.required' => 'The account field is required.',
            'account_id.exists' => 'The selected account is invalid.',
            'card_type.required' => 'The card type field is required.',
            'card_type.enum' => 'The selected card type is invalid.',
            'card_brand.required' => 'The card brand field is required.',
            'card_brand.enum' => 'The selected card brand is invalid.',
            'card_holder_name.required' => 'The card holder name field is required.',
            'card_holder_name.max' => 'The card holder name must not exceed 100 characters.',
        ];
    }
}
