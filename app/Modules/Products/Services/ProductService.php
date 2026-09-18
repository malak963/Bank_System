<?php

namespace App\Modules\Products\Services;

use App\Modules\Accounts\Models\Account;
use App\Modules\Customers\Models\Customer;
use App\Modules\Products\Enums\ProductStatus;
use App\Modules\Products\Enums\ProductType;
use App\Modules\Products\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductService
{
    public function createProduct(array $data): Product
    {
        $productType = ProductType::from($data['product_type']);
        
        $product = Product::create([
            'product_number' => $this->generateProductNumber(),
            'customer_id' => $data['customer_id'],
            'account_id' => $data['account_id'] ?? null,
            'branch_id' => $data['branch_id'] ?? null,
            'product_type' => $productType,
            'status' => ProductStatus::Active,
            'name' => $data['name'],
            'balance' => $data['balance'] ?? $productType->minBalance(),
            'currency' => $data['currency'] ?? 'USD',
            'interest_rate' => $data['interest_rate'] ?? null,
            'term_months' => $data['term_months'] ?? null,
            'maturity_date' => $data['term_months'] ? now()->addMonths($data['term_months']) : null,
            'auto_renew' => $data['auto_renew'] ?? false,
            'min_balance' => $data['min_balance'] ?? $productType->minBalance(),
            'max_balance' => $data['max_balance'] ?? null,
            'opened_at' => now(),
            'notes' => $data['notes'] ?? null,
            'metadata' => $data['metadata'] ?? null,
        ]);

        Log::info("Product created successfully", [
            'product_id' => $product->id,
            'product_number' => $product->product_number,
            'product_type' => $productType->value,
        ]);

        return $product;
    }

    public function openSavingsAccount(Customer $customer, Account $account, float $initialDeposit, array $options = []): Product
    {
        return $this->createProduct([
            'customer_id' => $customer->id,
            'account_id' => $account->id,
            'branch_id' => $account->branch_id,
            'product_type' => ProductType::SavingsAccount,
            'name' => $options['name'] ?? 'Savings Account',
            'balance' => $initialDeposit,
            'interest_rate' => $options['interest_rate'] ?? 2.5,
            'min_balance' => $options['min_balance'] ?? 100,
            'metadata' => $options,
        ]);
    }

    public function openFixedDeposit(Customer $customer, Account $account, float $amount, int $termMonths, float $interestRate, array $options = []): Product
    {
        return $this->createProduct([
            'customer_id' => $customer->id,
            'account_id' => $account->id,
            'branch_id' => $account->branch_id,
            'product_type' => ProductType::FixedDeposit,
            'name' => $options['name'] ?? "Fixed Deposit {$termMonths}M",
            'balance' => $amount,
            'interest_rate' => $interestRate,
            'term_months' => $termMonths,
            'auto_renew' => $options['auto_renew'] ?? false,
            'metadata' => $options,
        ]);
    }

    public function openCertificateOfDeposit(Customer $customer, Account $account, float $amount, int $termMonths, float $interestRate, array $options = []): Product
    {
        return $this->createProduct([
            'customer_id' => $customer->id,
            'account_id' => $account->id,
            'branch_id' => $account->branch_id,
            'product_type' => ProductType::CertificateOfDeposit,
            'name' => $options['name'] ?? "Certificate of Deposit {$termMonths}M",
            'balance' => $amount,
            'interest_rate' => $interestRate,
            'term_months' => $termMonths,
            'auto_renew' => $options['auto_renew'] ?? false,
            'metadata' => $options,
        ]);
    }

    public function openInvestmentAccount(Customer $customer, Account $account, float $initialDeposit, array $options = []): Product
    {
        return $this->createProduct([
            'customer_id' => $customer->id,
            'account_id' => $account->id,
            'branch_id' => $account->branch_id,
            'product_type' => ProductType::InvestmentAccount,
            'name' => $options['name'] ?? 'Investment Account',
            'balance' => $initialDeposit,
            'interest_rate' => $options['interest_rate'] ?? null,
            'min_balance' => $options['min_balance'] ?? 5000,
            'metadata' => array_merge($options, [
                'risk_level' => $options['risk_level'] ?? 'medium',
                'investment_strategy' => $options['investment_strategy'] ?? 'balanced',
            ]),
        ]);
    }

    public function closeProduct(Product $product, ?string $reason = null): Product
    {
        $product->close($reason);

        Log::info("Product closed", [
            'product_id' => $product->id,
            'product_number' => $product->product_number,
            'reason' => $reason,
        ]);

        return $product->fresh();
    }

    public function calculateInterest(Product $product): float
    {
        return $product->calculateInterest();
    }

    public function applyInterest(Product $product): Product
    {
        $product->applyInterest();

        Log::info("Interest applied to product", [
            'product_id' => $product->id,
            'product_number' => $product->product_number,
            'interest_amount' => $product->interest_earned,
        ]);

        return $product->fresh();
    }

    public function processMaturedProducts(): int
    {
        $maturedProducts = Product::matured()
            ->where('auto_renew', false)
            ->get();

        $processedCount = 0;

        foreach ($maturedProducts as $product) {
            $product->update(['status' => ProductStatus::Matured]);
            $processedCount++;

            Log::info("Product marked as matured", [
                'product_id' => $product->id,
                'product_number' => $product->product_number,
            ]);
        }

        return $processedCount;
    }

    public function processAutoRenewals(): int
    {
        $maturedProducts = Product::matured()
            ->where('auto_renew', true)
            ->get();

        $renewedCount = 0;

        foreach ($maturedProducts as $product) {
            // Create new product with same terms
            $newProduct = $this->createProduct([
                'customer_id' => $product->customer_id,
                'account_id' => $product->account_id,
                'branch_id' => $product->branch_id,
                'product_type' => $product->product_type,
                'name' => $product->name,
                'balance' => $product->balance + $product->interest_earned,
                'interest_rate' => $product->interest_rate,
                'term_months' => $product->term_months,
                'auto_renew' => $product->auto_renew,
                'min_balance' => $product->min_balance,
                'max_balance' => $product->max_balance,
                'metadata' => array_merge($product->metadata ?? [], [
                    'renewed_from' => $product->product_number,
                    'renewal_count' => ($product->metadata['renewal_count'] ?? 0) + 1,
                ]),
            ]);

            // Close old product
            $product->close('Auto-renewed to ' . $newProduct->product_number);

            $renewedCount++;

            Log::info("Product auto-renewed", [
                'old_product_id' => $product->id,
                'new_product_id' => $newProduct->id,
            ]);
        }

        return $renewedCount;
    }

    public function calculateDailyInterest(): int
    {
        $activeProducts = Product::active()
            ->whereNotNull('interest_rate')
            ->where('interest_rate', '>', 0)
            ->get();

        $processedCount = 0;

        foreach ($activeProducts as $product) {
            $this->applyInterest($product);
            $processedCount++;
        }

        return $processedCount;
    }

    public function getProducts(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = Product::query();

        if (isset($filters['customer_id'])) {
            $query->byCustomer($filters['customer_id']);
        }

        if (isset($filters['product_type'])) {
            $query->byType($filters['product_type']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['branch_id'])) {
            $query->byBranch($filters['branch_id']);
        }

        if (isset($filters['active_only']) && $filters['active_only']) {
            $query->active();
        }

        if (isset($filters['matured_only']) && $filters['matured_only']) {
            $query->matured();
        }

        return $query->with(['customer', 'account', 'branch'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getProductStatistics(): array
    {
        $total = Product::count();
        $active = Product::active()->count();
        $matured = Product::matured()->count();
        $closed = Product::where('status', ProductStatus::Closed)->count();
        
        $totalBalance = Product::active()->sum('balance');
        $totalInterestEarned = Product::sum('interest_earned');

        $byType = Product::selectRaw('product_type, COUNT(*) as count, SUM(balance) as total_balance')
            ->groupBy('product_type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->product_type => [
                    'count' => $item->count,
                    'total_balance' => $item->total_balance,
                ]];
            })
            ->toArray();

        return [
            'total' => $total,
            'active' => $active,
            'matured' => $matured,
            'closed' => $closed,
            'total_balance' => $totalBalance,
            'total_interest_earned' => $totalInterestEarned,
            'by_type' => $byType,
        ];
    }

    public function getExpiringProducts(int $days = 30): \Illuminate\Database\Eloquent\Collection
    {
        return Product::expiringSoon($days)
            ->with(['customer', 'account'])
            ->orderBy('maturity_date', 'asc')
            ->get();
    }

    private function generateProductNumber(): string
    {
        return 'PRD' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }
}
