<?php

namespace App\Modules\Products\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Products\Services\ProductService;
use App\Modules\Products\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(): View
    {
        $products = Product::with(['customer', 'account', 'branch'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $statistics = $this->productService->getProductStatistics();

        return view('products::index', compact('products', 'statistics'));
    }

    public function create(): View
    {
        $productTypes = \App\Modules\Products\Enums\ProductType::cases();
        $customers = \App\Modules\Customers\Models\Customer::all();
        $accounts = \App\Modules\Accounts\Models\Account::all();
        $branches = \App\Modules\Branches\Models\Branch::all();

        return view('products::create', compact('productTypes', 'customers', 'accounts', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'account_id' => 'nullable|exists:accounts,id',
            'branch_id' => 'nullable|exists:branches,id',
            'product_type' => 'required|string',
            'name' => 'required|string|max:200',
            'balance' => 'nullable|numeric|min:0',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'term_months' => 'nullable|integer|min:1',
            'auto_renew' => 'boolean',
            'min_balance' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        try {
            $product = $this->productService->createProduct($validated);
            return redirect()
                ->route('products.show', $product)
                ->with('success', 'Product created successfully');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Product creation failed: ' . $e->getMessage());
        }
    }

    public function show(Product $product): View
    {
        $product->load(['customer', 'account', 'branch', 'transactions']);
        return view('products::show', compact('product'));
    }

    public function close(Request $request, Product $product)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $product = $this->productService->closeProduct($product, $validated['reason'] ?? null);
            return redirect()
                ->route('products.show', $product)
                ->with('success', 'Product closed successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Close failed: ' . $e->getMessage());
        }
    }

    public function applyInterest(Product $product)
    {
        try {
            $product = $this->productService->applyInterest($product);
            return redirect()
                ->route('products.show', $product)
                ->with('success', 'Interest applied successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Interest application failed: ' . $e->getMessage());
        }
    }

    // API Methods
    public function apiIndex(Request $request): JsonResponse
    {
        $filters = $request->only(['customer_id', 'product_type', 'status', 'branch_id', 'active_only', 'matured_only']);
        $products = $this->productService->getProducts($filters);
        
        return response()->json($products);
    }

    public function apiShow(Product $product): JsonResponse
    {
        $product->load(['customer', 'account', 'branch', 'transactions']);
        return response()->json($product);
    }

    public function apiStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'account_id' => 'nullable|exists:accounts,id',
            'branch_id' => 'nullable|exists:branches,id',
            'product_type' => 'required|string',
            'name' => 'required|string|max:200',
            'balance' => 'nullable|numeric|min:0',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'term_months' => 'nullable|integer|min:1',
            'auto_renew' => 'boolean',
            'min_balance' => 'nullable|numeric|min:0',
        ]);

        try {
            $product = $this->productService->createProduct($validated);
            return response()->json($product, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function apiStatistics(): JsonResponse
    {
        $statistics = $this->productService->getProductStatistics();
        return response()->json($statistics);
    }
}
