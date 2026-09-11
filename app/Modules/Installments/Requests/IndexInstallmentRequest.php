<?php

namespace App\Modules\Installments\Requests;

use App\Modules\Installments\Enums\InstallmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexInstallmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in([...array_column(InstallmentStatus::cases(), 'value'), 'overdue'])],
            'due_from' => ['nullable', 'date'],
            'due_to' => ['nullable', 'date', 'after_or_equal:due_from'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['search' => $this->filled('search') ? trim((string) $this->input('search')) : $this->input('search')]);
    }

    public function filters(): array
    {
        return collect($this->validated())->filter(fn (mixed $value): bool => $value !== null && $value !== '')->all();
    }
}
