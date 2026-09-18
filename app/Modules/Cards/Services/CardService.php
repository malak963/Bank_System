<?php

namespace App\Modules\Cards\Services;

use App\Modules\Accounts\Models\Account;
use App\Modules\Cards\Enums\CardBrand;
use App\Modules\Cards\Enums\CardStatus;
use App\Modules\Cards\Enums\CardType;
use App\Modules\Cards\Models\Card;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CardService
{
    public function createCard(array $data): Card
    {
        return DB::transaction(function () use ($data) {
            $account = Account::findOrFail($data['account_id']);
            
            $this->validateCardRequest($account, is_string($data['card_type']) ? $data['card_type'] : $data['card_type']->value);

            $cardBrand = is_string($data['card_brand']) ? CardBrand::from($data['card_brand']) : $data['card_brand'];
            $cardNumber = $this->generateCardNumber($cardBrand);
            $cvv = $this->generateCVV($cardBrand);
            $pin = $this->generatePIN();
            
            $expiryDate = $this->calculateExpiryDate();

            $cardType = is_string($data['card_type']) ? CardType::from($data['card_type']) : $data['card_type'];
            
            $card = Card::create([
                'card_number' => $cardNumber,
                'card_holder_name' => $data['card_holder_name'],
                'card_type' => $cardType,
                'card_brand' => $cardBrand,
                'expiry_month' => $expiryDate->month,
                'expiry_year' => $expiryDate->year,
                'cvv' => $cvv,
                'pin' => $pin,
                'status' => CardStatus::Pending,
                'account_id' => $account->id,
                'customer_id' => $account->customer_id,
                'daily_limit' => $data['daily_limit'] ?? 5000,
                'monthly_limit' => $data['monthly_limit'] ?? 20000,
                'international_enabled' => $data['international_enabled'] ?? false,
                'online_enabled' => $data['online_enabled'] ?? true,
                'contactless_enabled' => $data['contactless_enabled'] ?? true,
                'issued_at' => now(),
                'expires_at' => $expiryDate,
                'metadata' => [
                    'delivery_method' => $data['delivery_method'] ?? 'branch',
                    'priority' => $data['priority'] ?? 'standard',
                    'requested_by' => auth()->id(),
                ],
            ]);

            Log::info("Card created successfully", [
                'card_id' => $card->id,
                'account_id' => $account->id,
                'card_type' => $data['card_type'],
            ]);

            return $card;
        });
    }

    public function activateCard(Card $card, string $pin): Card
    {
        if ($card->status !== CardStatus::Pending) {
            throw new \Exception('Card can only be activated from pending status');
        }

        if ($card->pin !== $pin) {
            throw new \Exception('Invalid PIN');
        }

        $card->update([
            'status' => CardStatus::Active,
            'activated_at' => now(),
        ]);

        Log::info("Card activated successfully", [
            'card_id' => $card->id,
            'account_id' => $card->account_id,
        ]);

        return $card->fresh();
    }

    public function blockCard(Card $card, string $reason, ?int $blockedBy = null): Card
    {
        if (!$card->isActive()) {
            throw new \Exception('Only active cards can be blocked');
        }

        $card->update([
            'status' => CardStatus::Blocked,
            'blocked_at' => now(),
            'block_reason' => $reason,
        ]);

        Log::warning("Card blocked", [
            'card_id' => $card->id,
            'account_id' => $card->account_id,
            'reason' => $reason,
            'blocked_by' => $blockedBy,
        ]);

        return $card->fresh();
    }

    public function unblockCard(Card $card, ?int $unblockedBy = null): Card
    {
        if (!$card->status->canBeUnblocked()) {
            throw new \Exception('This card cannot be unblocked');
        }

        if ($card->isExpired()) {
            throw new \Exception('Expired cards cannot be unblocked');
        }

        $card->update([
            'status' => CardStatus::Active,
            'blocked_at' => null,
            'block_reason' => null,
        ]);

        Log::info("Card unblocked successfully", [
            'card_id' => $card->id,
            'account_id' => $card->account_id,
            'unblocked_by' => $unblockedBy,
        ]);

        return $card->fresh();
    }

    public function replaceCard(Card $oldCard, string $reason, array $newCardData = []): Card
    {
        if (!$oldCard->status->requiresReplacement()) {
            throw new \Exception('This card does not require replacement');
        }

        return DB::transaction(function () use ($oldCard, $reason, $newCardData) {
            // Update old card status
            $oldCard->update([
                'status' => CardStatus::Replaced,
                'replacement_reason' => $reason,
            ]);

            // Create new card
            $newCard = $this->createCard([
                'account_id' => $oldCard->account_id,
                'card_type' => $oldCard->card_type->value,
                'card_brand' => $oldCard->card_brand->value,
                'card_holder_name' => $oldCard->card_holder_name,
                'daily_limit' => $newCardData['daily_limit'] ?? $oldCard->daily_limit,
                'monthly_limit' => $newCardData['monthly_limit'] ?? $oldCard->monthly_limit,
                'international_enabled' => $newCardData['international_enabled'] ?? $oldCard->international_enabled,
                'online_enabled' => $newCardData['online_enabled'] ?? $oldCard->online_enabled,
                'contactless_enabled' => $newCardData['contactless_enabled'] ?? $oldCard->contactless_enabled,
                'delivery_method' => $newCardData['delivery_method'] ?? 'branch',
                'priority' => $newCardData['priority'] ?? 'express',
            ]);

            // Link cards
            $newCard->update(['replaced_by_card_id' => $oldCard->id]);

            Log::info("Card replaced successfully", [
                'old_card_id' => $oldCard->id,
                'new_card_id' => $newCard->id,
                'reason' => $reason,
            ]);

            return $newCard;
        });
    }

    public function updatePIN(Card $card, string $currentPin, string $newPin): Card
    {
        if ($card->pin !== $currentPin) {
            throw new \Exception('Current PIN is incorrect');
        }

        if (strlen($newPin) !== 4 || !is_numeric($newPin)) {
            throw new \Exception('New PIN must be 4 digits');
        }

        $card->update(['pin' => $newPin]);

        Log::info("Card PIN updated", [
            'card_id' => $card->id,
            'account_id' => $card->account_id,
        ]);

        return $card->fresh();
    }

    public function updateLimits(Card $card, array $limits): Card
    {
        $validated = [];
        
        if (isset($limits['daily_limit'])) {
            $validated['daily_limit'] = min($limits['daily_limit'], 100000);
        }
        
        if (isset($limits['monthly_limit'])) {
            $validated['monthly_limit'] = min($limits['monthly_limit'], 1000000);
        }

        $card->update($validated);

        Log::info("Card limits updated", [
            'card_id' => $card->id,
            'limits' => $validated,
        ]);

        return $card->fresh();
    }

    public function toggleFeatures(Card $card, array $features): Card
    {
        $validated = [];
        
        if (isset($features['international_enabled'])) {
            $validated['international_enabled'] = $features['international_enabled'];
        }
        
        if (isset($features['online_enabled'])) {
            $validated['online_enabled'] = $features['online_enabled'];
        }
        
        if (isset($features['contactless_enabled'])) {
            $validated['contactless_enabled'] = $features['contactless_enabled'];
        }

        $card->update($validated);

        Log::info("Card features toggled", [
            'card_id' => $card->id,
            'features' => $validated,
        ]);

        return $card->fresh();
    }

    public function getCardsByAccount(Account $account): \Illuminate\Database\Eloquent\Collection
    {
        return $account->cards()->with(['account', 'customer'])->orderBy('created_at', 'desc')->get();
    }

    public function getCardsByCustomer(int $customerId): \Illuminate\Database\Eloquent\Collection
    {
        return Card::where('customer_id', $customerId)
            ->with(['account', 'customer'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getExpiringCards(int $days = 30): \Illuminate\Database\Eloquent\Collection
    {
        return Card::expiringSoon($days)
            ->with(['account', 'customer'])
            ->where('status', CardStatus::Active)
            ->get();
    }

    private function validateCardRequest(Account $account, string $cardType): void
    {
        if (!$account->isOpen()) {
            throw new \Exception('Account must be active to issue a card');
        }

        $cardTypeEnum = CardType::from($cardType);
        
        if ($cardTypeEnum->requiresCreditCheck()) {
            // Credit cards would require additional credit checks
            // This is a placeholder for credit check logic
        }

        // Check if account already has too many cards
        $activeCards = $account->cards()->where('status', CardStatus::Active)->count();
        if ($activeCards >= 5) {
            throw new \Exception('Maximum number of active cards reached for this account');
        }
    }

    private function generateCardNumber(CardBrand $brand): string
    {
        $prefix = collect($brand->startsWith())->random();
        $length = $brand->cardNumberLength();
        $remainingLength = $length - strlen($prefix);
        
        $remaining = '';
        for ($i = 0; $i < $remainingLength; $i++) {
            $remaining .= rand(0, 9);
        }
        
        return $prefix . $remaining;
    }

    private function generateCVV(CardBrand $brand): string
    {
        $length = $brand->cvvLength();
        $cvv = '';
        for ($i = 0; $i < $length; $i++) {
            $cvv .= rand(0, 9);
        }
        return $cvv;
    }

    private function generatePIN(): string
    {
        return str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    private function calculateExpiryDate(): \Illuminate\Support\Carbon
    {
        // Cards typically expire 3-5 years from issue
        $years = rand(3, 5);
        return now()->addYears($years);
    }
}
