<?php

namespace Database\Seeders;

use App\Modules\Branches\Models\Branch;
use App\Modules\Customers\Models\Customer;
use App\Modules\Queues\Models\Queue;
use Illuminate\Database\Seeder;

class QueueSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::where('code', 'BR-DAM-01')->first() ?? Branch::first();
        $ahmad = Customer::where('customer_number', 'CUST-000001')->first();
        $nour = Customer::where('customer_number', 'CUST-000004')->first();

        $queues = [
            [
                'branch_id' => $branch->id,
                'customer_id' => $ahmad?->id,
                'ticket_number' => 'A-101',
                'service_type' => 'general',
                'status' => 'serving',
                'priority' => 'high',
                'joined_at' => now()->subMinutes(15),
                'called_at' => now()->subMinutes(3),
                'serving_at' => now()->subMinutes(2),
                'service_counter' => 'Counter 1',
                'notes' => 'خدمة عملاء VIP - كبار المتعاملين',
            ],
            [
                'branch_id' => $branch->id,
                'customer_id' => $nour?->id,
                'ticket_number' => 'B-205',
                'service_type' => 'account_opening',
                'status' => 'waiting',
                'priority' => 'normal',
                'joined_at' => now()->subMinutes(8),
                'estimated_wait_time' => 12,
                'notes' => 'طلب فتح حساب توفير إضافي',
            ],
            [
                'branch_id' => $branch->id,
                'customer_id' => null,
                'ticket_number' => 'C-301',
                'service_type' => 'card_services',
                'status' => 'waiting',
                'priority' => 'normal',
                'joined_at' => now()->subMinutes(4),
                'estimated_wait_time' => 18,
                'notes' => 'تجديد بطاقة صراف آلي تالفة',
            ],
        ];

        foreach ($queues as $q) {
            Queue::updateOrCreate(
                [
                    'branch_id' => $q['branch_id'],
                    'ticket_number' => $q['ticket_number'],
                ],
                $q
            );
        }
    }
}
