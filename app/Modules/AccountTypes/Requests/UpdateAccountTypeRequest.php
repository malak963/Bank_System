<?php

namespace App\Modules\AccountTypes\Requests;

use App\Modules\AccountTypes\Enums\AccountTypeStatus;
use App\Modules\AccountTypes\Models\AccountType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAccountTypeRequest extends StoreAccountTypeRequest
{
    public function rules(): array
    {
        $accountType = $this->route('account_type');
        $accountTypeId = $accountType instanceof AccountType ? $accountType->id : $accountType;

        return [
            'code' => ['required', 'string', 'max:20', 'regex:/^[A-Z0-9_-]+$/', Rule::unique('account_types', 'code')->ignore($accountTypeId)],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'currency' => ['required', 'alpha', 'size:3'],
            'status' => ['required', Rule::enum(AccountTypeStatus::class)],
        ];
    }
}
