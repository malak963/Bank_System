<?php

namespace App\Modules\Customers\Requests;

use App\Modules\Customers\Enums\CustomerStatus;
use App\Modules\Customers\Enums\IdentityDocumentType;
use App\Modules\Customers\Enums\KycStatus;
use App\Modules\Customers\Enums\RiskLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'search' => [
                'nullable',
                'string',
                'max:100',
            ],

            'status' => [
                'nullable',
                Rule::enum(CustomerStatus::class),
            ],

            'kyc_status' => [
                'nullable',
                Rule::enum(KycStatus::class),
            ],

            'risk_level' => [
                'nullable',
                Rule::enum(RiskLevel::class),
            ],

            'identity_document_type' => [
                'nullable',
                Rule::enum(IdentityDocumentType::class),
            ],

            'identity_document_country' => [
                'nullable',
                'alpha',
                'size:2',
            ],

            'verified_from' => [
                'nullable',
                'date',
            ],

            'verified_to' => [
                'nullable',
                'date',
                'after_or_equal:verified_from',
            ],

            'sort' => [
                'nullable',
                Rule::in([
                    'latest',
                    'name',
                    'kyc_oldest',
                    'verification_newest',
                    'risk',
                    'document_expiry',
                ]),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'search' => $this->filled('search')
                ? trim((string) $this->input('search'))
                : $this->input('search'),
            'identity_document_country' => $this->filled('identity_document_country')
                ? strtoupper(trim((string) $this->input('identity_document_country')))
                : $this->input('identity_document_country'),
        ]);
    }

    public function filters(): array
    {
        return collect($this->validated())
            ->filter(fn (mixed $value): bool => $value !== null && $value !== '')
            ->all();
    }
}
