<?php

namespace App\Modules\Notifications\Services;

use App\Modules\Customers\Models\Customer;
use App\Modules\Notifications\Enums\NotificationChannel;
use App\Modules\Notifications\Enums\NotificationStatus;
use App\Modules\Notifications\Enums\NotificationType;
use App\Modules\Notifications\Models\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    public function createNotification(array $data): Notification
    {
        $notificationType = is_string($data['notification_type']) ? NotificationType::from($data['notification_type']) : $data['notification_type'];
        $channel = is_string($data['channel']) ? NotificationChannel::from($data['channel']) : $data['channel'];
        
        return Notification::create([
            'customer_id' => $data['customer_id'] ?? null,
            'notification_type' => $notificationType,
            'channel' => $channel,
            'status' => NotificationStatus::Pending,
            'subject' => $data['subject'] ?? null,
            'message' => $data['message'],
            'data' => $data['data'] ?? null,
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'priority' => $data['priority'] ?? 5,
            'metadata' => $data['metadata'] ?? null,
            'notifiable_type' => $data['notifiable_type'] ?? null,
            'notifiable_id' => $data['notifiable_id'] ?? null,
        ]);
    }

    public function sendNotification(Notification $notification): bool
    {
        try {
            $notification->update(['status' => NotificationStatus::Sent, 'sent_at' => now()]);

            $success = match ($notification->channel) {
                NotificationChannel::Email => $this->sendEmail($notification),
                NotificationChannel::SMS => $this->sendSMS($notification),
                NotificationChannel::Push => $this->sendPush($notification),
                NotificationChannel::InApp => $this->sendInApp($notification),
                NotificationChannel::WhatsApp => $this->sendWhatsApp($notification),
            };

            if ($success) {
                $notification->markAsDelivered();
                Log::info("Notification delivered successfully", [
                    'notification_id' => $notification->id,
                    'channel' => $notification->channel->value,
                ]);
                return true;
            } else {
                $notification->markAsFailed('Delivery failed');
                return false;
            }

        } catch (\Exception $e) {
            $notification->markAsFailed($e->getMessage());
            Log::error("Notification sending failed", [
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function sendTransactionAlert(Customer $customer, float $amount, string $type, array $data = []): Notification
    {
        $message = match ($type) {
            'credit' => "Your account has been credited with $" . number_format($amount, 2),
            'debit' => "Your account has been debited with $" . number_format($amount, 2),
            default => "Transaction of $" . number_format($amount, 2) . " processed",
        };

        return $this->createNotification([
            'customer_id' => $customer->id,
            'notification_type' => NotificationType::Transaction->value,
            'channel' => NotificationChannel::SMS->value,
            'subject' => 'Transaction Alert',
            'message' => $message,
            'data' => array_merge($data, ['amount' => $amount, 'type' => $type]),
            'priority' => 7,
        ]);
    }

    public function sendSecurityAlert(Customer $customer, string $message, array $data = []): Notification
    {
        return $this->createNotification([
            'customer_id' => $customer->id,
            'notification_type' => NotificationType::Security->value,
            'channel' => NotificationChannel::SMS->value,
            'subject' => 'Security Alert',
            'message' => $message,
            'data' => $data,
            'priority' => 10,
        ]);
    }

    public function sendCardAlert(Customer $customer, string $action, array $data = []): Notification
    {
        $message = match ($action) {
            'blocked' => 'Your card has been blocked for security reasons',
            'activated' => 'Your card has been activated successfully',
            'used' => 'Your card was used for a transaction',
            default => 'Card activity detected',
        };

        return $this->createNotification([
            'customer_id' => $customer->id,
            'notification_type' => NotificationType::Card->value,
            'channel' => NotificationChannel::SMS->value,
            'subject' => 'Card Alert',
            'message' => $message,
            'data' => array_merge($data, ['action' => $action]),
            'priority' => 8,
        ]);
    }

    public function sendBalanceAlert(Customer $customer, float $balance, array $data = []): Notification
    {
        return $this->createNotification([
            'customer_id' => $customer->id,
            'notification_type' => NotificationType::Balance->value,
            'channel' => NotificationChannel::SMS->value,
            'subject' => 'Balance Update',
            'message' => 'Your current account balance is $' . number_format($balance, 2),
            'data' => array_merge($data, ['balance' => $balance]),
            'priority' => 5,
        ]);
    }

    public function sendAppointmentReminder(Customer $customer, string $dateTime, array $data = []): Notification
    {
        return $this->createNotification([
            'customer_id' => $customer->id,
            'notification_type' => NotificationType::Appointment->value,
            'channel' => NotificationChannel::SMS->value,
            'subject' => 'Appointment Reminder',
            'message' => 'Reminder: Your appointment is scheduled for ' . $dateTime,
            'data' => array_merge($data, ['appointment_time' => $dateTime]),
            'priority' => 6,
            'scheduled_at' => now()->addHours(24),
        ]);
    }

    public function sendBulkNotification(array $customerIds, mixed $type, mixed $channel, string $message, array $data = []): \Illuminate\Database\Eloquent\Collection
    {
        $notificationType = is_string($type) ? NotificationType::from($type) : $type;
        $notificationChannel = is_string($channel) ? NotificationChannel::from($channel) : $channel;
        
        $notifications = collect();

        foreach ($customerIds as $customerId) {
            $notification = $this->createNotification([
                'customer_id' => $customerId,
                'notification_type' => $notificationType->value,
                'channel' => $notificationChannel->value,
                'subject' => $notificationType->label(),
                'message' => $message,
                'data' => $data,
                'priority' => $notificationType->isUrgent() ? 8 : 5,
            ]);
            $notifications->push($notification);
        }

        return $notifications;
    }

    public function retryFailedNotifications(): int
    {
        $failedNotifications = Notification::failed()
            ->where('retry_count', '<', 3)
            ->readyToSend()
            ->get();

        $retryCount = 0;

        foreach ($failedNotifications as $notification) {
            if ($this->sendNotification($notification)) {
                $retryCount++;
            }
        }

        return $retryCount;
    }

    public function processScheduledNotifications(): int
    {
        $scheduledNotifications = Notification::readyToSend()
            ->scheduled()
            ->get();

        $processedCount = 0;

        foreach ($scheduledNotifications as $notification) {
            if ($this->sendNotification($notification)) {
                $processedCount++;
            }
        }

        return $processedCount;
    }

    public function getCustomerNotifications(int $customerId, array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = Notification::byCustomer($customerId);

        if (isset($filters['type'])) {
            $type = is_string($filters['type']) ? NotificationType::from($filters['type']) : $filters['type'];
            $query->byType($type);
        }

        if (isset($filters['channel'])) {
            $channel = is_string($filters['channel']) ? NotificationChannel::from($filters['channel']) : $filters['channel'];
            $query->byChannel($channel);
        }

        if (isset($filters['status'])) {
            $status = is_string($filters['status']) ? NotificationStatus::from($filters['status']) : $filters['status'];
            $query->where('status', $status);
        }

        if (isset($filters['unread_only']) && $filters['unread_only']) {
            $query->unread();
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function markAsRead(int $notificationId): bool
    {
        $notification = Notification::findOrFail($notificationId);
        $notification->markAsRead();
        return true;
    }

    public function markAllAsRead(int $customerId): int
    {
        return Notification::byCustomer($customerId)
            ->unread()
            ->update([
                'status' => NotificationStatus::Read,
                'read_at' => now(),
            ]);
    }

    private function sendEmail(Notification $notification): bool
    {
        // Placeholder for email sending logic
        // In production, integrate with email service like SendGrid, Mailgun, etc.
        try {
            // Simulate email sending
            Log::info("Sending email notification", [
                'notification_id' => $notification->id,
                'customer_id' => $notification->customer_id,
                'subject' => $notification->subject,
            ]);
            
            // Simulate success
            return true;
        } catch (\Exception $e) {
            Log::error("Email sending failed", [
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    private function sendSMS(Notification $notification): bool
    {
        // Placeholder for SMS sending logic
        // In production, integrate with SMS service like Twilio, Nexmo, etc.
        try {
            Log::info("Sending SMS notification", [
                'notification_id' => $notification->id,
                'customer_id' => $notification->customer_id,
                'message' => substr($notification->message, 0, 50) . '...',
            ]);
            
            // Simulate success
            return true;
        } catch (\Exception $e) {
            Log::error("SMS sending failed", [
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    private function sendPush(Notification $notification): bool
    {
        // Placeholder for push notification logic
        // In production, integrate with FCM, APNs, or OneSignal
        try {
            Log::info("Sending push notification", [
                'notification_id' => $notification->id,
                'customer_id' => $notification->customer_id,
                'subject' => $notification->subject,
            ]);
            
            // Simulate success
            return true;
        } catch (\Exception $e) {
            Log::error("Push notification sending failed", [
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    private function sendInApp(Notification $notification): bool
    {
        // In-app notifications are stored in database and retrieved by frontend
        try {
            Log::info("Creating in-app notification", [
                'notification_id' => $notification->id,
                'customer_id' => $notification->customer_id,
            ]);
            
            // Simulate success
            return true;
        } catch (\Exception $e) {
            Log::error("In-app notification creation failed", [
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    private function sendWhatsApp(Notification $notification): bool
    {
        // Placeholder for WhatsApp sending logic
        // In production, integrate with WhatsApp Business API
        try {
            Log::info("Sending WhatsApp notification", [
                'notification_id' => $notification->id,
                'customer_id' => $notification->customer_id,
                'message' => substr($notification->message, 0, 50) . '...',
            ]);
            
            // Simulate success
            return true;
        } catch (\Exception $e) {
            Log::error("WhatsApp sending failed", [
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
