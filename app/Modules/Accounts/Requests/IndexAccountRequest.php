<?php

namespace App\Modules\Accounts\Requests;

use App\Modules\Accounts\Enums\AccountStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::enum(AccountStatus::class)],
            'account_type_id' => ['nullable', 'integer', Rule::exists('account_types', 'id')],
            'sort' => ['nullable', Rule::in(['latest', 'balance'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'search' => $this->filled('search') ? trim((string) $this->input('search')) : $this->input('search'),
        ]);
    }

    public function filters(): array
    {
        return collect($this->validated())
            ->filter(fn (mixed $value): bool => $value !== null && $value !== '')
            ->all();
    }
}
