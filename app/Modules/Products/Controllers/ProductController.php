<?php

namespace App\Modules\Products\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounts\Models\Account;
use App\Modules\Customers\Models\Customer;
use App\Modules\Products\Enums\ProductType;
use App\Modules\Products\Models\Product;
use App\Modules\Products\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    public function index(Request $request): View
    {
        $products = $this->productService->getProducts($request->all());
        $statistics = $this->productService->getStatistics();

        return view('products::index', compact('products', 'statistics'));
    }

    public function create(): View
    {
        $productTypes = ProductType::cases();
        $customers = Customer::query()->orderBy('first_name')->get();
        $accounts = Account::query()->orderBy('account_number')->get();

        return view('products::create', compact('productTypes', 'customers', 'accounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'product_type' => 'required|string',
            'customer_id' => 'required|exists:customers,id',
            'account_id' => 'nullable|exists:accounts,id',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'term_months' => 'nullable|integer|min:1|max:360',
            'initial_balance' => 'nullable|numeric|min:0',
            'minimum_balance' => 'nullable|numeric|min:0',
            'interest_calculation_frequency' => 'nullable|string',
            'terms_and_conditions' => 'nullable|string',
        ]);

        $customer = Customer::find($validated['customer_id']);

        $productData = [
            'name' => $validated['name'],
            'product_type' => $validated['product_type'],
            'customer_id' => $validated['customer_id'],
            'account_id' => $validated['account_id'] ?? null,
            'branch_id' => $customer?->branch_id,
            'interest_rate' => $validated['interest_rate'],
            'term_months' => $validated['term_months'] ?? null,
            'balance' => $validated['initial_balance'] ?? 0,
            'min_balance' => $validated['minimum_balance'] ?? 0,
            'currency' => 'USD',
            'notes' => $validated['terms_and_conditions'] ?? null,
            'metadata' => [
                'interest_calculation_frequency' => $validated['interest_calculation_frequency'] ?? 'monthly',
            ],
            'opened_at' => now(),
        ];

        if (!empty($productData['term_months'])) {
            $productData['maturity_date'] = now()->addMonths((int) $productData['term_months']);
        }

        $this->productService->createProduct($productData);

        return redirect()->route('products.index')->with('success', __('Product created successfully.'));
    }

    public function show(Product $product): View
    {
        $product->load(['customer', 'account', 'branch']);
        return view('products::show', compact('product'));
    }

    public function close(Product $product): RedirectResponse
    {
        $this->productService->closeProduct($product);
        return back()->with('success', __('Product closed successfully.'));
    }

    public function applyInterest(Product $product): RedirectResponse
    {
        $this->productService->applyInterest($product);
        return back()->with('success', __('Interest applied successfully.'));
    }

    // API methods
    public function apiIndex(Request $request): JsonResponse
    {
        return response()->json($this->productService->getProducts($request->all()));
    }

    public function apiStore(Request $request): JsonResponse
    {
        $product = $this->productService->createProduct($request->all());
        return response()->json($product, 201);
    }

    public function apiShow(Product $product): JsonResponse
    {
        return response()->json($product->load(['customer', 'account', 'branch']));
    }

    public function apiStatistics(): JsonResponse
    {
        return response()->json($this->productService->getStatistics());
    }
}
