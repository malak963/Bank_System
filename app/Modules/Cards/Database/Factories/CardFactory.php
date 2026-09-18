<?php

namespace App\Modules\Cards\Database\Factories;

use App\Modules\Accounts\Models\Account;
use App\Modules\Cards\Enums\CardBrand;
use App\Modules\Cards\Enums\CardStatus;
use App\Modules\Cards\Enums\CardType;
use App\Modules\Cards\Models\Card;
use App\Modules\Customers\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CardFactory extends Factory
{
    protected $model = Card::class;

    public function definition(): array
    {
        $cardBrand = $this->faker->randomElement(CardBrand::cases());
        $cardNumber = $this->generateCardNumber($cardBrand);
        $expiryDate = $this->faker->dateTimeBetween('+1 year', '+5 years');
        
        return [
            'card_number' => $cardNumber,
            'card_holder_name' => $this->faker->name(),
            'card_type' => $this->faker->randomElement(CardType::cases()),
            'card_brand' => $cardBrand,
            'expiry_month' => (int)$expiryDate->format('m'),
            'expiry_year' => (int)$expiryDate->format('Y'),
            'cvv' => $this->generateCVV($cardBrand),
            'pin' => $this->faker->numerify('####'),
            'status' => CardStatus::Active,
            'account_id' => Account::factory(),
            'customer_id' => Customer::factory(),
            'daily_limit' => $this->faker->randomFloat(2, 1000, 10000),
            'monthly_limit' => $this->faker->randomFloat(2, 5000, 50000),
            'international_enabled' => $this->faker->boolean(70),
            'online_enabled' => $this->faker->boolean(90),
            'contactless_enabled' => $this->faker->boolean(80),
            'issued_at' => $this->faker->dateTimeBetween('-2 years', '-1 month'),
            'expires_at' => $expiryDate,
            'activated_at' => $this->faker->dateTimeBetween('-1 year', '-1 week'),
            'last_used_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'metadata' => $this->faker->optional()->randomElements([
                'issue_location' => $this->faker->city(),
                'delivery_method' => $this->faker->randomElement(['branch', 'mail', 'courier']),
                'priority' => $this->faker->randomElement(['standard', 'express', 'urgent']),
            ], $this->faker->numberBetween(0, 3)),
        ];
    }

    public function debit(): self
    {
        return $this->state(fn (array $attributes) => [
            'card_type' => CardType::Debit,
        ]);
    }

    public function credit(): self
    {
        return $this->state(fn (array $attributes) => [
            'card_type' => CardType::Credit,
        ]);
    }

    public function virtual(): self
    {
        return $this->state(fn (array $attributes) => [
            'card_type' => CardType::Virtual,
            'issued_at' => now(),
            'activated_at' => now(),
        ]);
    }

    public function blocked(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => CardStatus::Blocked,
            'blocked_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'block_reason' => $this->faker->randomElement([
                'Lost card',
                'Stolen card',
                'Suspicious activity',
                'Customer request',
                'Damaged card',
            ]),
        ]);
    }

    public function expired(): self
    {
        $expiryDate = $this->faker->dateTimeBetween('-2 years', '-1 month');
        
        return $this->state(fn (array $attributes) => [
            'status' => CardStatus::Expired,
            'expiry_month' => (int)$expiryDate->format('m'),
            'expiry_year' => (int)$expiryDate->format('Y'),
            'expires_at' => $expiryDate,
        ]);
    }

    public function pending(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => CardStatus::Pending,
            'issued_at' => null,
            'activated_at' => null,
        ]);
    }

    private function generateCardNumber(CardBrand $brand): string
    {
        $prefix = $this->faker->randomElement($brand->startsWith());
        $length = $brand->cardNumberLength();
        $remainingLength = $length - strlen($prefix);
        
        $remaining = '';
        for ($i = 0; $i < $remainingLength; $i++) {
            $remaining .= $this->faker->randomDigit();
        }
        
        return $prefix . $remaining;
    }

    private function generateCVV(CardBrand $brand): string
    {
        $length = $brand->cvvLength();
        return $this->faker->numerify(str_repeat('#', $length));
    }
}
