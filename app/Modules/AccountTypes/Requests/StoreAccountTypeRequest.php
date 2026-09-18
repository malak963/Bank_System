<?php

namespace App\Modules\AccountTypes\Requests;

use App\Modules\AccountTypes\Enums\AccountTypeStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAccountTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:20', 'regex:/^[A-Z0-9_-]+$/', Rule::unique('account_types', 'code')],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'currency' => ['required', 'alpha', 'size:3'],
            'status' => ['required', Rule::enum(AccountTypeStatus::class)],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => $this->filled('code') ? strtoupper(trim((string) $this->input('code'))) : $this->input('code'),
            'currency' => $this->filled('currency') ? strtoupper(trim((string) $this->input('currency'))) : $this->input('currency'),
            'name' => $this->filled('name') ? trim((string) $this->input('name')) : $this->input('name'),
        ]);
    }
}
