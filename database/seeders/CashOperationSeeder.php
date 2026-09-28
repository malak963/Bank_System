<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Accounts\Models\Account;
use App\Modules\Branches\Models\Branch;
use App\Modules\CashManagement\Models\CashOperation;
use Illuminate\Database\Seeder;

class CashOperationSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::where('code', 'BR-DAM-01')->first() ?? Branch::first();
        $teller = User::where('email', 'teller@bank.com')->first();
        $manager = User::where('email', 'manager@bank.com')->first();
        $account = Account::where('account_number', '1000100001')->first();

        $operations = [
            [
                'operation_reference' => 'CSH-2026-0001',
                'branch_id' => $branch->id,
                'teller_id' => $teller?->id,
                'account_id' => null,
                'operation_type' => 'replenishment',
                'amount' => 50000000.00,
                'currency' => 'SYP',
                'status' => 'completed',
                'description' => 'تغذية صندوق الصراف الصباحية - Morning Teller Drawer Cash Replenishment',
                'notes' => 'تم استلام وتدقيق المبلغ من القاصة الرئيسية للفرع',
                'approved_by' => $manager?->id,
                'approved_at' => now()->subDays(2)->setHour(8)->setMinute(30),
                'completed_at' => now()->subDays(2)->setHour(8)->setMinute(35),
                'operation_date' => now()->subDays(2),
            ],
            [
                'operation_reference' => 'CSH-2026-0002',
                'branch_id' => $branch->id,
                'teller_id' => $teller?->id,
                'account_id' => $account?->id,
                'operation_type' => 'deposit',
                'amount' => 5000000.00,
                'currency' => 'SYP',
                'status' => 'completed',
                'description' => 'إيداع نقدي مباشر في الحساب - Client Over-The-Counter Cash Deposit',
                'notes' => 'تم عد وفحص العملة بواسطة جهاز الكشف الآلي',
                'approved_by' => $manager?->id,
                'approved_at' => now()->subDays(2)->setHour(11)->setMinute(15),
                'completed_at' => now()->subDays(2)->setHour(11)->setMinute(20),
                'counterparty_name' => 'Ahmad Al-Sayed',
                'counterparty_id' => 'NAT0102008801',
                'operation_date' => now()->subDays(2),
            ],
            [
                'operation_reference' => 'CSH-2026-0003',
                'branch_id' => $branch->id,
                'teller_id' => $teller?->id,
                'account_id' => null,
                'operation_type' => 'withdrawal_to_vault',
                'amount' => 30000000.00,
                'currency' => 'SYP',
                'status' => 'completed',
                'description' => 'توريد فوائض نقدية إلى القاصة المركزية - EOD Vault Surplus Deposit',
                'notes' => 'تسوية نهاية اليوم المصرفي وإغلاق دراج الصندوق',
                'approved_by' => $manager?->id,
                'approved_at' => now()->subDays(2)->setHour(15)->setMinute(0),
                'completed_at' => now()->subDays(2)->setHour(15)->setMinute(10),
                'operation_date' => now()->subDays(2),
            ],
        ];

        foreach ($operations as $op) {
            CashOperation::updateOrCreate(
                ['operation_reference' => $op['operation_reference']],
                $op
            );
        }
    }
}
