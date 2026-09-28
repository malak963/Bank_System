<?php

namespace Database\Seeders;

use App\Modules\Accounts\Models\Account;
use App\Modules\Branches\Models\Branch;
use App\Modules\Customers\Models\Customer;
use App\Modules\Products\Enums\ProductStatus;
use App\Modules\Products\Enums\ProductType;
use App\Modules\Products\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::where('code', 'BR-DAM-01')->first() ?? Branch::first();
        $ahmad = Customer::where('customer_number', 'CUST-000001')->first();
        $ahmadAcc = Account::where('account_number', '1000100002')->first();

        $omar = Customer::where('customer_number', 'CUST-000003')->first();
        $omarAcc = Account::where('account_number', '1000300002')->first();

        $products = [
            [
                'product_number' => 'PRD-FD-001',
                'customer_id' => $ahmad?->id,
                'account_id' => $ahmadAcc?->id,
                'branch_id' => $branch->id,
                'product_type' => ProductType::FixedDeposit,
                'status' => ProductStatus::Active,
                'name' => 'وديعة استثمارية ثابتة لأجل 12 شهراً',
                'balance' => 20000000.00,
                'currency' => 'SYP',
                'interest_rate' => 11.50,
                'term_months' => 12,
                'maturity_date' => now()->addMonths(8),
                'auto_renew' => true,
                'min_balance' => 5000000.00,
                'interest_earned' => 766666.00,
                'opened_at' => now()->subMonths(4),
                'notes' => 'توزيع العوائد ربع سنوي يضاف إلى الحساب الجاري المرتبط',
            ],
            [
                'product_number' => 'PRD-CD-002',
                'customer_id' => $omar?->id,
                'account_id' => $omarAcc?->id,
                'branch_id' => $branch->id,
                'product_type' => ProductType::CertificateOfDeposit,
                'status' => ProductStatus::Active,
                'name' => 'شهادة استثمار الذهب التراكمية للشركات',
                'balance' => 50000000.00,
                'currency' => 'SYP',
                'interest_rate' => 12.25,
                'term_months' => 24,
                'maturity_date' => now()->addMonths(18),
                'auto_renew' => false,
                'min_balance' => 10000000.00,
                'interest_earned' => 3062500.00,
                'opened_at' => now()->subMonths(6),
                'notes' => 'شهادة استثمارية ذات عائد تراكمي يصرف عند تاريخ الاستحقاق',
            ],
        ];

        foreach ($products as $p) {
            if (!$p['customer_id']) {
                continue;
            }

            Product::updateOrCreate(
                ['product_number' => $p['product_number']],
                $p
            );
        }
    }
}
