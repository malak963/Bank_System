<?php

namespace App\Modules\Customers\Requests;

use App\Modules\Customers\Enums\CustomerStatus;
use App\Modules\Customers\Enums\IdentityDocumentType;
use App\Modules\Customers\Enums\KycStatus;
use App\Modules\Customers\Enums\RiskLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
                Rule::unique('customers', 'user_id'),
            ],

            'customer_number' => [
                'required',
                'string',
                'max:32',
                'regex:/^[A-Z0-9-]+$/',
                Rule::unique('customers', 'customer_number'),
            ],

            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'last_name' => [
                'required',
                'string',
                'max:100',
            ],

            'date_of_birth' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            'national_id' => [
                'required',
                'string',
                'max:64',
                Rule::unique('customers', 'national_id'),
            ],

            'identity_document_type' => [
                'nullable',
                Rule::enum(IdentityDocumentType::class),
            ],

            'identity_document_number' => [
                'nullable',
                'required_with:identity_document_type',
                'string',
                'max:80',
            ],

            'identity_document_country' => [
                'nullable',
                'alpha',
                'size:2',
            ],

            'identity_document_expires_at' => [
                'nullable',
                'date',
                'after:today',
            ],

            'phone_number' => [
                'nullable',
                'string',
                'max:32',
                "regex:/^\+?[0-9\s\-()]{7,32}$/",
            ],

            'address' => [
                'nullable',
                'string',
                'max:255',
            ],

            'status' => [
                'required',
                Rule::enum(CustomerStatus::class),
            ],

            'kyc_status' => [
                'required',
                Rule::enum(KycStatus::class),
            ],

            'kyc_reference' => [
                'nullable',
                'string',
                'max:64',
                'regex:/^[A-Z0-9-]+$/',
                Rule::unique('customers', 'kyc_reference'),
            ],

            'kyc_reviewed_by' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id'),
            ],

            'kyc_reviewed_at' => [
                'nullable',
                'date',
            ],

            'kyc_rejection_reason' => [
                'nullable',
                'required_if:kyc_status,rejected',
                'string',
                'max:1000',
            ],

            'risk_level' => [
                'required',
                Rule::enum(RiskLevel::class),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'customer_number' => $this->filled('customer_number')
                ? strtoupper(trim((string) $this->input('customer_number')))
                : $this->input('customer_number'),
            'national_id' => $this->filled('national_id')
                ? strtoupper(trim((string) $this->input('national_id')))
                : $this->input('national_id'),
            'identity_document_number' => $this->filled('identity_document_number')
                ? strtoupper(trim((string) $this->input('identity_document_number')))
                : $this->input('identity_document_number'),
            'identity_document_country' => $this->filled('identity_document_country')
                ? strtoupper(trim((string) $this->input('identity_document_country')))
                : $this->input('identity_document_country'),
            'kyc_reference' => $this->filled('kyc_reference')
                ? strtoupper(trim((string) $this->input('kyc_reference')))
                : $this->input('kyc_reference'),
            'first_name' => $this->filled('first_name')
                ? trim((string) $this->input('first_name'))
                : $this->input('first_name'),
            'last_name' => $this->filled('last_name')
                ? trim((string) $this->input('last_name'))
                : $this->input('last_name'),
        ]);
    }
}
