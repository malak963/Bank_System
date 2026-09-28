<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Customers\Models\Customer;
use App\Modules\Security\Enums\SecurityEventType;
use App\Modules\Security\Enums\SecurityLevel;
use App\Modules\Security\Models\SecurityEvent;
use Illuminate\Database\Seeder;

class SecurityEventSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@bank.com')->first();
        $manager = User::where('email', 'manager@bank.com')->first();
        $ahmad = Customer::where('customer_number', 'CUST-000001')->first();

        $events = [
            [
                'event_type' => SecurityEventType::Login,
                'security_level' => SecurityLevel::Low,
                'user_id' => $admin?->id,
                'customer_id' => null,
                'ip_address' => '192.168.1.10',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
                'location' => 'Damascus, Syria',
                'description' => 'تسجيل دخول ناجح لحساب المشرف العام - Administrator Authenticated',
                'source' => 'web',
                'is_resolved' => true,
                'resolved_at' => now(),
                'resolved_by' => $admin?->id,
                'related_entity_type' => User::class,
                'related_entity_id' => $admin?->id ?? 1,
            ],
            [
                'event_type' => SecurityEventType::FailedLogin,
                'security_level' => SecurityLevel::Medium,
                'user_id' => null,
                'customer_id' => $ahmad?->id,
                'ip_address' => '82.137.200.45',
                'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X)',
                'location' => 'Damascus, Syria',
                'description' => 'محاولة تسجيل دخول فاشلة بسبب كلمة مرور غير صحيحة - Invalid Password Attempt',
                'source' => 'mobile',
                'is_resolved' => true,
                'resolved_at' => now()->subDay(),
                'resolved_by' => $manager?->id,
                'resolution_notes' => 'قام العميل بإعادة تعيين كلمة المرور بنجاح واستعاد الدخول الطبيعي',
                'related_entity_type' => Customer::class,
                'related_entity_id' => $ahmad?->id ?? 1,
            ],
            [
                'event_type' => SecurityEventType::LargeTransaction,
                'security_level' => SecurityLevel::High,
                'user_id' => null,
                'customer_id' => $ahmad?->id,
                'ip_address' => '82.137.200.45',
                'location' => 'Damascus, Syria',
                'description' => 'رصد عملية مالية ذات سقف مرتفع - High Value Wire Transfer Initiated',
                'source' => 'web',
                'is_resolved' => true,
                'resolved_at' => now()->subDays(2),
                'resolved_by' => $manager?->id,
                'action_taken' => 'تم التواصل هاتفياً مع صاحب الحساب وتأكيد صحة العملية والمستفيد',
                'related_entity_type' => Customer::class,
                'related_entity_id' => $ahmad?->id ?? 1,
            ],
        ];

        foreach ($events as $ev) {
            SecurityEvent::create($ev);
        }
    }
}
