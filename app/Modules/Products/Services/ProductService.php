<?php

namespace App\Modules\Products\Services;

use App\Modules\Products\Models\Product;
use App\Modules\Products\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    public function getProducts(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Product::query()->with(['customer', 'account', 'branch']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['product_type'])) {
            $query->where('product_type', $filters['product_type']);
        }
        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function createProduct(array $data): Product
    {
        if (empty($data['product_number'])) {
            $data['product_number'] = 'PRD-' . strtoupper(uniqid());
        }
        if (empty($data['status'])) {
            $data['status'] = ProductStatus::Active;
        }

        return Product::create($data);
    }

    public function closeProduct(Product $product): bool
    {
        return $product->update([
            'status' => ProductStatus::Closed,
            'closed_at' => now(),
        ]);
    }

    public function applyInterest(Product $product): bool
    {
        if ($product->status !== ProductStatus::Active) {
            return false;
        }

        $interest = ($product->balance * ($product->interest_rate / 100)) / 12;
        $product->interest_earned += $interest;
        $product->balance += $interest;
        $product->last_interest_calculation = now();

        return $product->save();
    }

    public function getStatistics(): array
    {
        $total = Product::count();
        $active = Product::where('status', ProductStatus::Active)->count();
        $totalBalance = (float) (Product::sum('balance') ?? 0);
        $totalInterestEarned = (float) (Product::sum('interest_earned') ?? 0);

        return [
            'total' => $total,
            'active' => $active,
            'total_products' => $total,
            'active_products' => $active,
            'total_balance' => $totalBalance,
            'total_interest_earned' => $totalInterestEarned,
        ];
    }
}
