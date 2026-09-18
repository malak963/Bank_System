<?php

namespace App\Modules\Customers\Database\Factories;

use App\Models\User;
use App\Modules\Customers\Enums\CustomerStatus;
use App\Modules\Customers\Enums\IdentityDocumentType;
use App\Modules\Customers\Enums\KycStatus;
use App\Modules\Customers\Enums\RiskLevel;
use App\Modules\Customers\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'customer_number' => 'CUST-'.$this->faker->unique()->numerify('######'),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'date_of_birth' => $this->faker->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d'),
            'national_id' => $this->faker->unique()->bothify('NAT########'),
            'identity_document_type' => IdentityDocumentType::NationalId,
            'identity_document_number' => $this->faker->unique()->bothify('DOC########'),
            'identity_document_country' => $this->faker->countryCode(),
            'identity_document_expires_at' => $this->faker->dateTimeBetween('+1 year', '+10 years')->format('Y-m-d'),
            'phone_number' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'status' => CustomerStatus::Active,
            'kyc_status' => KycStatus::Pending,
            'kyc_reference' => null,
            'kyc_reviewed_by' => null,
            'kyc_reviewed_at' => null,
            'kyc_rejection_reason' => null,
            'risk_level' => RiskLevel::Low,
        ];
    }
}
