<?php

namespace App\Modules\Branches\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $branch = $this->route('branch');

        return [
            'code' => ['sometimes', 'string', 'max:20', Rule::unique('branches', 'code')->ignore($branch->id)],
            'name' => ['sometimes', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email'],
            'manager_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
        ];
    }
}
