<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Branches\Models\Branch;
use App\Modules\CustomerService\Enums\TicketCategory;
use App\Modules\CustomerService\Enums\TicketPriority;
use App\Modules\CustomerService\Enums\TicketStatus;
use App\Modules\CustomerService\Models\Ticket;
use App\Modules\CustomerService\Models\TicketResponse;
use App\Modules\Customers\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerServiceSeeder extends Seeder
{
    public function run(): void
    {
        $csUser = User::where('email', 'cs@bank.com')->first();
        $branch = Branch::where('code', 'BR-DAM-01')->first() ?? Branch::first();

        $ahmad = Customer::where('customer_number', 'CUST-000001')->first();
        $layla = Customer::where('customer_number', 'CUST-000002')->first();
        $omar = Customer::where('customer_number', 'CUST-000003')->first();

        $ticketsData = [
            [
                'ticket_number' => 'TCK-2026-0001',
                'customer_id' => $ahmad?->id,
                'branch_id' => $branch->id,
                'category' => TicketCategory::Card,
                'priority' => TicketPriority::High,
                'status' => TicketStatus::Resolved,
                'subject' => 'طلب رفع سقف المشتريات اليومي للبطاقة الائتمانية',
                'description' => 'أرجو التكرم برفع سقف الشراء اليومي عبر الإنترنت لبطاقة الفيزا لتغطية رسوم مؤتمر مهني دولي.',
                'assigned_to' => $csUser?->id,
                'resolved_by' => $csUser?->id,
                'resolution' => 'تم التحقق من بيانات الحساب وسجل العميل، وتمت الموافقة على رفع السقف اليومي مؤقتاً لمدة 7 أيام.',
                'resolved_at' => now()->subDays(3),
                'closed_at' => now()->subDays(2),
                'first_response_at' => now()->subDays(4),
                'customer_satisfaction' => 5,
            ],
            [
                'ticket_number' => 'TCK-2026-0002',
                'customer_id' => $layla?->id,
                'branch_id' => $branch->id,
                'category' => TicketCategory::Transaction,
                'priority' => TicketPriority::Normal,
                'status' => TicketStatus::InProgress,
                'subject' => 'استفسار عن تأكيد استلام حوالة خارجية',
                'description' => 'قمت بإرسال حوالة مصرفية خارجية عبر سويفت منذ يومين وأرغب بالحصول على إشعار السويفت MT103 المحدث.',
                'assigned_to' => $csUser?->id,
                'first_response_at' => now()->subHours(8),
            ],
            [
                'ticket_number' => 'TCK-2026-0003',
                'customer_id' => $omar?->id,
                'branch_id' => $branch->id,
                'category' => TicketCategory::General,
                'priority' => TicketPriority::Normal,
                'status' => TicketStatus::Open,
                'subject' => 'طلب تفعيل خدمة كشف الحساب الإلكتروني الدوري للشركات',
                'description' => 'نود تفعيل إرسال كشف الحساب الشهري بصيغة ملفات مشفرة للمدير المالي للشركة.',
                'assigned_to' => $csUser?->id,
            ],
        ];

        foreach ($ticketsData as $data) {
            if (!$data['customer_id']) {
                continue;
            }

            $ticket = Ticket::updateOrCreate(
                ['ticket_number' => $data['ticket_number']],
                $data
            );

            // Add response if resolved
            if ($ticket->status === TicketStatus::Resolved && $ticket->responses()->count() === 0) {
                TicketResponse::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $csUser?->id,
                    'customer_id' => null,
                    'message' => 'عزيزي العميل، تم رفع سقف مشتريات بطاقتكم بنجاح. نتمنى لكم تجربة مصرفية متميزة.',
                    'is_internal' => false,
                ]);
            }
        }
    }
}
