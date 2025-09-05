<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockAdjustmentRequest;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class InventoryController extends Controller
{
    /**
     * Create a new InventoryController instance.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Add stock to a product.
     */
    public function addStock(StockAdjustmentRequest $request): JsonResponse
    {
        $currentUser = auth()->user();
        if (!$currentUser->isAdmin() && !$currentUser->isManager()) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_UNAUTHORIZED',
                'message' => 'Only managers and administrators can add stock',
            ], 403);
        }

        // Override request type to 'in'
        $request->merge(['type' => 'in']);

        $product = Product::find($request->product_id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_PRODUCT_NOT_FOUND',
                'message' => 'Product not found',
            ], 404);
        }

        try {
            $stockMovement = $product->adjustStock(
                $request->quantity,
                'in',
                $request->reason,
                $request->notes,
                $request->unit_cost,
                $request->reference
            );

            $product->load('category');

            return response()->json([
                'status' => 'success',
                'data' => [
                    'product' => $product,
                    'stock_movement' => $stockMovement,
                    'message' => 'Stock added successfully',
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_STOCK_ADJUSTMENT_FAILED',
                'message' => 'Failed to add stock',
            ], 500);
        }
    }

    /**
     * Remove stock from a product.
     */
    public function removeStock(StockAdjustmentRequest $request): JsonResponse
    {
        $currentUser = auth()->user();
        if (!$currentUser->isAdmin() && !$currentUser->isManager()) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_UNAUTHORIZED',
                'message' => 'Only managers and administrators can remove stock',
            ], 403);
        }

        // Override request type to 'out'
        $request->merge(['type' => 'out']);

        $product = Product::find($request->product_id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_PRODUCT_NOT_FOUND',
                'message' => 'Product not found',
            ], 404);
        }

        // Check if there's enough stock
        if ($product->stock < $request->quantity) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_INSUFFICIENT_STOCK',
                'message' => "Insufficient stock. Available: {$product->stock}, Requested: {$request->quantity}",
            ], 400);
        }

        try {
            $stockMovement = $product->adjustStock(
                $request->quantity,
                'out',
                $request->reason,
                $request->notes,
                null, // No unit cost for outbound movements
                $request->reference
            );

            $product->load('category');

            return response()->json([
                'status' => 'success',
                'data' => [
                    'product' => $product,
                    'stock_movement' => $stockMovement,
                    'message' => 'Stock removed successfully',
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_STOCK_ADJUSTMENT_FAILED',
                'message' => 'Failed to remove stock',
            ], 500);
        }
    }

    /**
     * Get stock movements for a product.
     */
    public function getProductStockMovements(Request $request, int $productId): JsonResponse
    {
        if (!Gate::allows('accessInventory', auth()->user())) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_UNAUTHORIZED',
                'message' => 'You are not authorized to view stock movements',
            ], 403);
        }

        $product = Product::find($productId);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_PRODUCT_NOT_FOUND',
                'message' => 'Product not found',
            ], 404);
        }

        $query = StockMovement::with(['user'])
            ->where('product_id', $productId)
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('reason')) {
            $query->where('reason', $request->reason);
        }

        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $perPage = min($request->get('per_page', 15), 100);
        $stockMovements = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => [
                'product' => $product->load('category'),
                'stock_movements' => $stockMovements->items(),
                'pagination' => [
                    'current_page' => $stockMovements->currentPage(),
                    'last_page' => $stockMovements->lastPage(),
                    'per_page' => $stockMovements->perPage(),
                    'total' => $stockMovements->total(),
                ],
            ],
        ]);
    }

    /**
     * Get all stock movements (admin/manager only).
     */
    public function getAllStockMovements(Request $request): JsonResponse
    {
        $currentUser = auth()->user();
        if (!$currentUser->isAdmin() && !$currentUser->isManager()) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_UNAUTHORIZED',
                'message' => 'Only managers and administrators can view all stock movements',
            ], 403);
        }

        $query = StockMovement::with(['product', 'user'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('reason')) {
            $query->where('reason', $request->reason);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $perPage = min($request->get('per_page', 15), 100);
        $stockMovements = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => [
                'stock_movements' => $stockMovements->items(),
                'pagination' => [
                    'current_page' => $stockMovements->currentPage(),
                    'last_page' => $stockMovements->lastPage(),
                    'per_page' => $stockMovements->perPage(),
                    'total' => $stockMovements->total(),
                ],
            ],
        ]);
    }

    /**
     * Get products that need reordering.
     */
    public function getLowStockProducts(Request $request): JsonResponse
    {
        if (!Gate::allows('accessInventory', auth()->user())) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_UNAUTHORIZED',
                'message' => 'You are not authorized to view inventory reports',
            ], 403);
        }

        $query = Product::with(['category'])
            ->needsReordering()
            ->active()
            ->orderBy('stock', 'asc');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $perPage = min($request->get('per_page', 50), 100);
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
}
