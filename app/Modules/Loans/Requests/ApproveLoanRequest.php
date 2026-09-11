<?php

namespace App\Modules\Loans\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'approved_amount' => ['nullable', 'numeric', 'min:0.01', 'max:999999999999.99'],
            'first_payment_date' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }
}
