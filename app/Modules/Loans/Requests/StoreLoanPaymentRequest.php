<?php

namespace App\Modules\Loans\Requests;

use App\Modules\Loans\Enums\LoanPaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLoanPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999999.99'],
            'payment_method' => ['required', Rule::enum(LoanPaymentMethod::class)],
            'paid_at' => ['nullable', 'date', 'before_or_equal:now'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
