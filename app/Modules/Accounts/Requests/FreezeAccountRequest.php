<?php

namespace App\Modules\Accounts\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FreezeAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'freeze_reason' => ['nullable', 'string', 'max:500'],
        ];
    }
}
