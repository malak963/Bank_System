<?php

namespace App\Modules\Users\Requests;

use App\Models\User;
use App\Modules\Users\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageUsers() ?? false;
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $userId = $user instanceof User ? $user->id : $user;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^\+?[0-9]{7,20}$/',
                Rule::unique('users', 'phone')->ignore($userId),
            ],

            'password' => [
                'nullable',
                'confirmed',
                Rules\Password::defaults(),
            ],

            'role' => [
                'required',
                Rule::enum(UserRole::class),
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->filled('name') ? trim((string) $this->input('name')) : $this->input('name'),
            'email' => $this->filled('email') ? strtolower(trim((string) $this->input('email'))) : $this->input('email'),
            'phone' => $this->filled('phone') ? preg_replace('/[\s\-()]/', '', (string) $this->input('phone')) : $this->input('phone'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
