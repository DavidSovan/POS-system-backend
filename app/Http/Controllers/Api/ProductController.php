<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    /**
     * Create a new ProductController instance.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of products.
     */
    public function index(Request $request): JsonResponse
    {
        if (!Gate::allows('accessInventory', auth()->user())) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_UNAUTHORIZED',
                'message' => 'You are not authorized to view products',
            ], 403);
        }

        $query = Product::with(['category']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('low_stock') && $request->low_stock) {
            $query->needsReordering();
        }

        if ($request->has('out_of_stock') && $request->out_of_stock) {
            $query->outOfStock();
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        
        if (in_array($sortBy, ['name', 'sku', 'price', 'stock', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        }

        // Pagination
        $perPage = min($request->get('per_page', 15), 100);
        $products = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => [
                'products' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ],
            ],
        ]);
    }

    /**
     * Store a newly created product.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $product = Product::create($request->validated());
            $product->load('category');

            // Create initial stock movement if stock > 0
            if ($product->stock > 0) {
                $product->adjustStock(
                    $product->stock,
                    'in',
                    'initial_stock',
                    'Initial stock when product was created',
                    $request->cost,
                    'INITIAL'
                );
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'product' => $product,
                    'message' => 'Product created successfully',
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_PRODUCT_CREATION_FAILED',
                'message' => 'Failed to create product',
            ], 500);
        }
    }

    /**
     * Display the specified product.
     */
    public function show(int $id): JsonResponse
    {
        if (!Gate::allows('accessInventory', auth()->user())) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_UNAUTHORIZED',
                'message' => 'You are not authorized to view products',
            ], 403);
        }

        $product = Product::with(['category', 'recentStockMovements.user'])->find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_PRODUCT_NOT_FOUND',
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'product' => $product,
                'needs_reordering' => $product->needsReordering(),
                'profit_margin' => $product->profit_margin,
                'profit_amount' => $product->profit_amount,
            ],
        ]);
    }

    /**
     * Update the specified product.
     */
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_PRODUCT_NOT_FOUND',
                'message' => 'Product not found',
            ], 404);
        }

        try {
            $product->update($request->validated());
            $product->load('category');

            return response()->json([
                'status' => 'success',
                'data' => [
                    'product' => $product,
                    'message' => 'Product updated successfully',
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_PRODUCT_UPDATE_FAILED',
                'message' => 'Failed to update product',
            ], 500);
        }
    }

    /**
     * Remove the specified product.
     */
    public function destroy(int $id): JsonResponse
    {
        $currentUser = auth()->user();
        if (!$currentUser->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_UNAUTHORIZED',
                'message' => 'Only administrators can delete products',
            ], 403);
        }

        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_PRODUCT_NOT_FOUND',
                'message' => 'Product not found',
            ], 404);
        }

        try {
            $product->delete();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'message' => 'Product deleted successfully',
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_PRODUCT_DELETE_FAILED',
                'message' => 'Failed to delete product',
            ], 500);
        }
    }
}
