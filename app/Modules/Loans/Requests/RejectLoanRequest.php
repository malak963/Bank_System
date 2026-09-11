<?php

namespace App\Modules\Loans\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return ['rejection_reason' => ['required', 'string', 'max:1000']];
    }
}
