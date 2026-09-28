<?php

namespace Database\Seeders;

use App\Modules\Customers\Models\Customer;
use App\Modules\Notifications\Enums\NotificationChannel;
use App\Modules\Notifications\Enums\NotificationStatus;
use App\Modules\Notifications\Enums\NotificationType;
use App\Modules\Notifications\Models\Notification;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $ahmad = Customer::where('customer_number', 'CUST-000001')->first();
        $layla = Customer::where('customer_number', 'CUST-000002')->first();

        $notifications = [
            [
                'customer_id' => $ahmad?->id,
                'notifiable_type' => Customer::class,
                'notifiable_id' => $ahmad?->id,
                'notification_type' => NotificationType::Transaction,
                'channel' => NotificationChannel::SMS,
                'status' => NotificationStatus::Delivered,
                'subject' => 'إشعار إيداع نقدي',
                'message' => 'عزيزي العميل، تم إيداع مبلغ 5,000,000 ل.س في حسابكم رقم 1000100001 بنجاح.',
                'sent_at' => now()->subDays(2),
                'delivered_at' => now()->subDays(2),
                'read_at' => now()->subDays(2),
                'priority' => 8,
            ],
            [
                'customer_id' => $ahmad?->id,
                'notifiable_type' => Customer::class,
                'notifiable_id' => $ahmad?->id,
                'notification_type' => NotificationType::Security,
                'channel' => NotificationChannel::Email,
                'status' => NotificationStatus::Delivered,
                'subject' => 'تنبيه أمني: تسجيل دخول جديد للخدمات المصرفية',
                'message' => 'تم تسجيل دخول جديد إلى حسابكم الإلكتروني من جهاز موثوق.',
                'sent_at' => now()->subDays(1),
                'delivered_at' => now()->subDays(1),
                'read_at' => now()->subDays(1),
                'priority' => 10,
            ],
            [
                'customer_id' => $layla?->id,
                'notifiable_type' => Customer::class,
                'notifiable_id' => $layla?->id,
                'notification_type' => NotificationType::Card,
                'channel' => NotificationChannel::Push,
                'status' => NotificationStatus::Delivered,
                'subject' => 'تفعيل بطاقة الصراف الآلي',
                'message' => 'تم تفعيل بطاقتكم المصرفية بنجاح، ويمكنكم استخدامها الآن في كافة أجهزة الصراف الآلي ونقاط البيع.',
                'sent_at' => now()->subDays(5),
                'delivered_at' => now()->subDays(5),
                'read_at' => null,
                'priority' => 7,
            ],
            [
                'customer_id' => $ahmad?->id,
                'notifiable_type' => Customer::class,
                'notifiable_id' => $ahmad?->id,
                'notification_type' => NotificationType::Marketing,
                'channel' => NotificationChannel::InApp,
                'status' => NotificationStatus::Pending,
                'subject' => 'عرض خاص: عوائد تفضيلية على الودائع الاستثمارية',
                'message' => 'استفد من معدلات فائدة تصاعدية تصل حتى 12.5% على الودائع السنوية الجديدة لفترة محدودة.',
                'priority' => 4,
            ],
        ];

        foreach ($notifications as $n) {
            if (!$n['customer_id']) {
                continue;
            }

            Notification::create($n);
        }
    }
}
