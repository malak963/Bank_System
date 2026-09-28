<?php

namespace Database\Seeders;

use App\Modules\Appointments\Models\Appointment;
use App\Modules\Branches\Models\Branch;
use App\Modules\Customers\Models\Customer;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::where('code', 'BR-DAM-01')->first() ?? Branch::first();
        $mezzeh = Branch::where('code', 'BR-DAM-02')->first() ?? $branch;

        $ahmad = Customer::where('customer_number', 'CUST-000001')->first();
        $layla = Customer::where('customer_number', 'CUST-000002')->first();
        $omar = Customer::where('customer_number', 'CUST-000003')->first();

        $appointments = [
            [
                'branch_id' => $branch->id,
                'customer_id' => $ahmad?->id,
                'service_type' => 'wealth_management',
                'status' => 'confirmed',
                'appointment_date' => now()->addDays(2)->format('Y-m-d'),
                'appointment_time' => now()->addDays(2)->setHour(10)->setMinute(30),
                'estimated_duration' => 30,
                'notes' => 'استشارة بخصوص الودائع الاستثمارية طويلة الأجل - Long-term Investment Portfolio Consultation',
                'confirmation_sent_at' => now()->subDay(),
            ],
            [
                'branch_id' => $mezzeh->id,
                'customer_id' => $layla?->id,
                'service_type' => 'card_services',
                'status' => 'completed',
                'appointment_date' => now()->subDays(3)->format('Y-m-d'),
                'appointment_time' => now()->subDays(3)->setHour(12)->setMinute(0),
                'estimated_duration' => 15,
                'notes' => 'استلام بطاقة فيزا إلكترونية وتفعيل الخدمات الدولية - Contactless Visa Issuance',
                'checked_in_at' => now()->subDays(3)->setHour(11)->setMinute(55),
                'started_at' => now()->subDays(3)->setHour(12)->setMinute(2),
                'completed_at' => now()->subDays(3)->setHour(12)->setMinute(18),
            ],
            [
                'branch_id' => $branch->id,
                'customer_id' => $omar?->id,
                'service_type' => 'loan_consultation',
                'status' => 'confirmed',
                'appointment_date' => now()->addDays(4)->format('Y-m-d'),
                'appointment_time' => now()->addDays(4)->setHour(11)->setMinute(0),
                'estimated_duration' => 45,
                'notes' => 'دراسة طلب التمويل التجاري وتقديم المستندات المالية - Commercial Credit Facility Review',
                'confirmation_sent_at' => now()->subHours(5),
            ],
        ];

        foreach ($appointments as $app) {
            if (!$app['customer_id']) {
                continue;
            }

            Appointment::updateOrCreate(
                [
                    'branch_id' => $app['branch_id'],
                    'customer_id' => $app['customer_id'],
                    'appointment_date' => $app['appointment_date'],
                ],
                $app
            );
        }
    }
}
