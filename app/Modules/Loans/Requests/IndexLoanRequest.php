<?php

namespace App\Modules\Loans\Requests;

use App\Modules\Loans\Enums\LoanStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::enum(LoanStatus::class)],
            'loan_type_id' => ['nullable', 'integer', Rule::exists('loan_types', 'id')],
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
