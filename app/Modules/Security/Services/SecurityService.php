<?php

namespace App\Modules\Security\Services;

use App\Modules\Accounts\Models\Account;
use App\Modules\Cards\Models\Card;
use App\Modules\Customers\Models\Customer;
use App\Modules\Security\Enums\SecurityEventType;
use App\Modules\Security\Enums\SecurityLevel;
use App\Modules\Security\Models\SecurityEvent;
use App\Modules\Transactions\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class SecurityService
{
    public function logSecurityEvent(array $data): SecurityEvent
    {
        $securityLevel = $data['security_level'] ?? $this->determineSecurityLevel($data['event_type']);

        $event = SecurityEvent::create([
            'event_type' => $data['event_type'],
            'security_level' => $securityLevel,
            'user_id' => $data['user_id'] ?? auth()->id(),
            'customer_id' => $data['customer_id'] ?? null,
            'ip_address' => $data['ip_address'] ?? Request::ip(),
            'user_agent' => $data['user_agent'] ?? Request::userAgent(),
            'device_id' => $data['device_id'] ?? null,
            'location' => $data['location'] ?? null,
            'description' => $data['description'] ?? null,
            'details' => $data['details'] ?? null,
            'source' => $data['source'] ?? 'web',
            'metadata' => $data['metadata'] ?? null,
            'related_entity_type' => $data['related_entity_type'] ?? null,
            'related_entity_id' => $data['related_entity_id'] ?? null,
        ]);

        // Auto-resolve low priority events
        if ($securityLevel === SecurityLevel::Low) {
            $event->resolve('Auto-resolved - low priority event');
        }

        // Auto-block critical events
        if ($securityLevel === SecurityLevel::Critical && $data['auto_block'] ?? true) {
            $event->block(now()->addDays(7));
            $this->sendSecurityAlert($event);
        }

        Log::info("Security event logged", [
            'event_id' => $event->id,
            'event_type' => $event->event_type->value,
            'security_level' => $event->security_level->value,
        ]);

        return $event;
    }

    public function logLogin(int $userId, bool $success = true, array $details = []): SecurityEvent
    {
        return $this->logSecurityEvent([
            'event_type' => $success ? SecurityEventType::Login : SecurityEventType::FailedLogin,
            'user_id' => $userId,
            'description' => $success ? 'User login successful' : 'Failed login attempt',
            'details' => array_merge($details, ['success' => $success]),
            'security_level' => $success ? SecurityLevel::Low : SecurityLevel::Medium,
        ]);
    }

    public function logTransaction(Transaction $transaction, array $details = []): SecurityEvent
    {
        $securityLevel = $this->assessTransactionRisk($transaction);

        return $this->logSecurityEvent([
            'event_type' => $securityLevel === SecurityLevel::High ? SecurityEventType::LargeTransaction : SecurityEventType::Transaction,
            'user_id' => auth()->id(),
            'customer_id' => $transaction->customer_id,
            'description' => $securityLevel === SecurityLevel::High ? 'Large transaction detected' : 'Transaction performed',
            'details' => array_merge($details, [
                'transaction_id' => $transaction->id,
                'amount' => $transaction->amount,
                'account_id' => $transaction->account_id,
            ]),
            'security_level' => $securityLevel,
            'related_entity_type' => Transaction::class,
            'related_entity_id' => $transaction->id,
        ]);
    }

    public function logCardBlock(Card $card, string $reason, array $details = []): SecurityEvent
    {
        return $this->logSecurityEvent([
            'event_type' => SecurityEventType::CardBlock,
            'customer_id' => $card->customer_id,
            'description' => "Card blocked: {$reason}",
            'details' => array_merge($details, [
                'card_id' => $card->id,
                'card_number' => $card->maskCardNumber(),
                'reason' => $reason,
            ]),
            'security_level' => SecurityLevel::High,
            'related_entity_type' => Card::class,
            'related_entity_id' => $card->id,
        ]);
    }

    public function logAccountFreeze(Account $account, string $reason, array $details = []): SecurityEvent
    {
        return $this->logSecurityEvent([
            'event_type' => SecurityEventType::AccountFreeze,
            'customer_id' => $account->customer_id,
            'description' => "Account frozen: {$reason}",
            'details' => array_merge($details, [
                'account_id' => $account->id,
                'account_number' => $account->account_number,
                'reason' => $reason,
            ]),
            'security_level' => SecurityLevel::High,
            'related_entity_type' => Account::class,
            'related_entity_id' => $account->id,
        ]);
    }

    public function logFraudAlert(Customer $customer, string $reason, array $details = []): SecurityEvent
    {
        return $this->logSecurityEvent([
            'event_type' => SecurityEventType::FraudAlert,
            'customer_id' => $customer->id,
            'description' => "Fraud alert: {$reason}",
            'details' => array_merge($details, ['reason' => $reason]),
            'security_level' => SecurityLevel::Critical,
            'auto_block' => true,
        ]);
    }

    public function logSuspiciousActivity(int $customerId, string $activity, array $details = []): SecurityEvent
    {
        return $this->logSecurityEvent([
            'event_type' => SecurityEventType::SuspiciousActivity,
            'customer_id' => $customerId,
            'description' => "Suspicious activity: {$activity}",
            'details' => array_merge($details, ['activity' => $activity]),
            'security_level' => SecurityLevel::High,
        ]);
    }

    public function resolveSecurityEvent(SecurityEvent $event, string $resolutionNotes, ?int $resolvedBy = null): SecurityEvent
    {
        $event->resolve($resolutionNotes, $resolvedBy);

        Log::info("Security event resolved", [
            'event_id' => $event->id,
            'resolved_by' => $resolvedBy,
            'resolution_notes' => $resolutionNotes,
        ]);

        return $event->fresh();
    }

    public function blockUser(int $userId, \DateTime $until = null, string $reason = ''): void
    {
        // Log the blocking event
        $this->logSecurityEvent([
            'event_type' => SecurityEventType::AccountFreeze,
            'user_id' => $userId,
            'description' => "User blocked: {$reason}",
            'security_level' => SecurityLevel::High,
        ]);

        // In a real implementation, you would block the user account
        // This is a placeholder for the actual blocking logic
        Log::warning("User blocked", [
            'user_id' => $userId,
            'until' => $until?->format('Y-m-d H:i:s'),
            'reason' => $reason,
        ]);
    }

    public function unblockUser(int $userId): void
    {
        // Log the unblocking event
        $this->logSecurityEvent([
            'event_type' => SecurityEventType::AccountFreeze,
            'user_id' => $userId,
            'description' => 'User unblocked',
            'security_level' => SecurityLevel::Low,
        ]);

        Log::info("User unblocked", ['user_id' => $userId]);
    }

    public function getSecurityEvents(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = SecurityEvent::query();

        if (isset($filters['user_id'])) {
            $query->byUser($filters['user_id']);
        }

        if (isset($filters['customer_id'])) {
            $query->byCustomer($filters['customer_id']);
        }

        if (isset($filters['event_type'])) {
            $query->byType($filters['event_type']);
        }

        if (isset($filters['security_level'])) {
            $query->where('security_level', $filters['security_level']);
        }

        if (isset($filters['unresolved_only']) && $filters['unresolved_only']) {
            $query->unresolved();
        }

        if (isset($filters['blocked_only']) && $filters['blocked_only']) {
            $query->blocked();
        }

        if (isset($filters['hours'])) {
            $query->recent($filters['hours']);
        }

        return $query->with(['user', 'customer', 'resolver'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getCriticalEvents(): \Illuminate\Database\Eloquent\Collection
    {
        return SecurityEvent::critical()
            ->unresolved()
            ->with(['user', 'customer'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getFraudAlerts(): \Illuminate\Database\Eloquent\Collection
    {
        return SecurityEvent::byType(SecurityEventType::FraudAlert)
            ->unresolved()
            ->with(['user', 'customer'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function assessTransactionRisk(Transaction $transaction): SecurityLevel
    {
        // Simple risk assessment - in production, this would be more sophisticated
        $riskFactors = 0;

        // Large amount
        if ($transaction->amount > 10000) {
            $riskFactors += 2;
        }

        // Unusual time (late night transactions)
        if ($transaction->created_at->hour >= 23 || $transaction->created_at->hour <= 5) {
            $riskFactors += 1;
        }

        // New account (less than 30 days old)
        if ($transaction->account && $transaction->account->opened_at && $transaction->account->opened_at->diffInDays(now()) < 30) {
            $riskFactors += 2;
        }

        // Determine security level based on risk factors
        return match (true) {
            $riskFactors >= 4 => SecurityLevel::Critical,
            $riskFactors >= 2 => SecurityLevel::High,
            $riskFactors >= 1 => SecurityLevel::Medium,
            default => SecurityLevel::Low,
        };
    }

    public function detectAnomalousPattern(int $customerId): array
    {
        $recentEvents = SecurityEvent::byCustomer($customerId)
            ->recent(24)
            ->get();

        $anomalies = [];

        // Check for multiple failed logins
        $failedLogins = $recentEvents->where('event_type', SecurityEventType::FailedLogin)->count();
        if ($failedLogins >= 5) {
            $anomalies[] = [
                'type' => 'multiple_failed_logins',
                'severity' => 'high',
                'count' => $failedLogins,
                'description' => "Multiple failed login attempts detected: {$failedLogins} in 24 hours",
            ];
        }

        // Check for unusual locations
        $locations = $recentEvents->whereNotNull('location')->pluck('location')->unique();
        if ($locations->count() >= 3) {
            $anomalies[] = [
                'type' => 'unusual_locations',
                'severity' => 'medium',
                'locations' => $locations->toArray(),
                'description' => 'Login attempts from multiple unusual locations',
            ];
        }

        // Check for rapid transactions
        $transactions = $recentEvents->where('event_type', SecurityEventType::Transaction)->count();
        if ($transactions >= 10) {
            $anomalies[] = [
                'type' => 'rapid_transactions',
                'severity' => 'high',
                'count' => $transactions,
                'description' => "High volume of transactions: {$transactions} in 24 hours",
            ];
        }

        return $anomalies;
    }

    private function determineSecurityLevel(SecurityEventType $eventType): SecurityLevel
    {
        return match ($eventType) {
            SecurityEventType::Login, SecurityEventType::Logout => SecurityLevel::Low,
            SecurityEventType::FailedLogin, SecurityEventType::PasswordChange, SecurityEventType::PasswordReset => SecurityLevel::Medium,
            SecurityEventType::Transaction, SecurityEventType::UnusualLocation, SecurityEventType::CardBlock => SecurityLevel::High,
            SecurityEventType::LargeTransaction, SecurityEventType::AccountFreeze, SecurityEventType::SuspiciousActivity => SecurityLevel::High,
            SecurityEventType::FraudAlert => SecurityLevel::Critical,
            default => SecurityLevel::Medium,
        };
    }

    private function sendSecurityAlert(SecurityEvent $event): void
    {
        // In production, this would send alerts via various channels
        // For now, we'll just log it
        Log::critical("Security alert triggered", [
            'event_id' => $event->id,
            'event_type' => $event->event_type->value,
            'customer_id' => $event->customer_id,
            'user_id' => $event->user_id,
        ]);

        // You could integrate with the Notifications module here
        // $notificationService->sendSecurityAlert($customer, $event->description);
    }
}
