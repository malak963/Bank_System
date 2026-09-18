<?php

namespace App\Modules\Transfers\Database\Factories;

use App\Modules\Accounts\Models\Account;
use App\Modules\Customers\Models\Customer;
use App\Modules\Transfers\Enums\TransferStatus;
use App\Modules\Transfers\Enums\TransferType;
use App\Modules\Transfers\Models\Transfer;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransferFactory extends Factory
{
    protected $model = Transfer::class;

    public function definition(): array
    {
        $transferType = $this->faker->randomElement(TransferType::cases());
        $status = $this->faker->randomElement(TransferStatus::cases());
        
        return [
            'transfer_reference' => 'TRF' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'from_account_id' => Account::factory(),
            'to_account_id' => $transferType === TransferType::Internal ? Account::factory() : null,
            'customer_id' => Customer::factory(),
            'transfer_type' => $transferType,
            'status' => $status,
            'amount' => $this->faker->randomFloat(2, 100, 10000),
            'currency' => 'USD',
            'exchange_rate' => $transferType === TransferType::International ? $this->faker->randomFloat(6, 0.5, 2) : null,
            'converted_amount' => $transferType === TransferType::International ? $this->faker->randomFloat(2, 50, 20000) : null,
            'fees' => $this->faker->randomFloat(2, 0, 100),
            'total_deducted' => 0,
            'recipient_name' => $transferType->requiresBeneficiaryBank() ? $this->faker->name() : null,
            'recipient_account' => $transferType->requiresBeneficiaryBank() ? $this->faker->bankAccountNumber() : null,
            'recipient_bank' => $transferType->requiresBeneficiaryBank() ? $this->faker->company() : null,
            'recipient_bank_address' => $transferType->requiresBeneficiaryBank() ? $this->faker->address() : null,
            'swift_code' => $transferType->requiresSwiftCode() ? $this->faker->swiftBicNumber() : null,
            'iban' => $transferType->requiresBeneficiaryBank() ? $this->faker->iban() : null,
            'routing_number' => $transferType->requiresBeneficiaryBank() ? $this->faker->randomNumber(9) : null,
            'reference' => $this->faker->optional()->numerify('REF########'),
            'description' => $this->faker->optional()->sentence(),
            'scheduled_for' => $transferType->isScheduled() ? $this->faker->dateTimeBetween('+1 day', '+1 month') : null,
            'processed_at' => $status !== TransferStatus::Pending ? $this->faker->dateTimeBetween('-1 week', 'now') : null,
            'completed_at' => $status === TransferStatus::Completed ? $this->faker->dateTimeBetween('-1 week', 'now') : null,
            'failed_at' => $status === TransferStatus::Failed ? $this->faker->dateTimeBetween('-1 week', 'now') : null,
            'failure_reason' => $status === TransferStatus::Failed ? $this->faker->randomElement(['Insufficient funds', 'Invalid recipient', 'Bank rejected', 'Network error']) : null,
            'cancelled_at' => $status === TransferStatus::Cancelled ? $this->faker->dateTimeBetween('-1 week', 'now') : null,
            'cancellation_reason' => $status === TransferStatus::Cancelled ? $this->faker->sentence() : null,
            'metadata' => $this->faker->optional()->randomElements([
                'source' => $this->faker->randomElement(['web', 'mobile', 'api']),
                'device' => $this->faker->word(),
                'priority' => $this->faker->randomElement(['normal', 'urgent']),
            ], $this->faker->numberBetween(0, 3)),
        ];
    }

    public function internal(): self
    {
        return $this->state(fn (array $attributes) => [
            'transfer_type' => TransferType::Internal,
            'to_account_id' => Account::factory(),
        ]);
    }

    public function external(): self
    {
        return $this->state(fn (array $attributes) => [
            'transfer_type' => TransferType::External,
            'to_account_id' => null,
        ]);
    }

    public function international(): self
    {
        return $this->state(fn (array $attributes) => [
            'transfer_type' => TransferType::International,
            'to_account_id' => null,
        ]);
    }

    public function completed(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => TransferStatus::Completed,
            'processed_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'completed_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    public function failed(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => TransferStatus::Failed,
            'failed_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'failure_reason' => $this->faker->randomElement(['Insufficient funds', 'Invalid recipient', 'Bank rejected']),
        ]);
    }

    public function scheduled(): self
    {
        return $this->state(fn (array $attributes) => [
            'transfer_type' => $this->faker->randomElement([TransferType::StandingOrder, TransferType::DirectDebit]),
            'scheduled_for' => $this->faker->dateTimeBetween('+1 day', '+1 month'),
        ]);
    }
}
