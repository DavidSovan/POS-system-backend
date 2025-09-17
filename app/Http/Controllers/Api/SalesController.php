<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutSaleRequest;
use App\Http\Requests\StoreSaleItemRequest;
use App\Http\Requests\StoreSaleRequest;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Start a new sale transaction.
     */
    public function store(StoreSaleRequest $request): JsonResponse
    {
        $user = auth()->user();

        $sale = Sale::create([
            'cashier_id' => $user->id,
            'total_amount' => 0,
            'payment_method' => null,
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'sale_id' => $sale->id,
                'message' => 'Sale started successfully',
            ],
        ], 201);
    }

    /**
     * Add an item to a sale.
     */
    public function addItem(int $saleId, StoreSaleItemRequest $request): JsonResponse
    {
        $user = auth()->user();

        $sale = Sale::with('saleItems')->find($saleId);
        if (!$sale) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_SALE_NOT_FOUND',
                'message' => 'Sale not found',
            ], 404);
        }

        // Ensure the sale belongs to the current user (cashier)
        if ($sale->cashier_id !== $user->id) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_UNAUTHORIZED',
                'message' => 'You are not authorized to modify this sale',
            ], 403);
        }

        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_PRODUCT_NOT_FOUND',
                'message' => 'Product not found',
            ], 404);
        }

        // Determine price (use provided price or default to product price)
        $price = $request->price !== null ? (float) $request->price : (float) $product->price;
        $quantity = (int) $request->quantity;
        $discount = (float) ($request->discount ?? 0);

        // Validate stock availability
        if ($product->stock < $quantity) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_INSUFFICIENT_STOCK',
                'message' => "Insufficient stock. Available: {$product->stock}, Requested: {$quantity}",
            ], 400);
        }

        // Validate discount does not exceed total price for quantity
        $gross = $quantity * $price;
        if ($discount > $gross) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_INVALID_DISCOUNT',
                'message' => 'Discount cannot exceed total price for the quantity',
            ], 422);
        }

        try {
            $result = DB::transaction(function () use ($sale, $product, $quantity, $price, $discount) {
                // Calculate subtotal
                $subtotal = $gross = $quantity * $price;
                $subtotal -= $discount;

                // Create sale item
                $saleItem = SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'discount' => $discount,
                    'subtotal' => $subtotal,
                ]);

                // Adjust stock (out)
                $product->adjustStock($quantity, 'out', 'sale', "Sale #{$sale->id} - Item added", null, (string) $sale->id);

                // Update sale total amount
                $sale->increment('total_amount', $subtotal);

                // Reload relations
                $sale->load(['saleItems.product']);

                return [$saleItem, $sale];
            });

            [, $updatedSale] = $result;

            return response()->json([
                'status' => 'success',
                'data' => [
                    'sale' => $updatedSale,
                    'message' => 'Item added to sale successfully',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_SALE_ITEM_ADD_FAILED',
                'message' => 'Failed to add item to sale',
            ], 500);
        }
    }

    /**
     * Show a sale with receipt-friendly formatting.
     */
    public function show(int $saleId): JsonResponse
    {
        $user = auth()->user();

        $sale = Sale::with(['saleItems.product', 'payment', 'cashier'])->find($saleId);
        if (!$sale) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_SALE_NOT_FOUND',
                'message' => 'Sale not found',
            ], 404);
        }

        // Access control: cashiers can only access their own sales
        if ($sale->cashier_id !== $user->id) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_UNAUTHORIZED',
                'message' => 'You are not authorized to view this sale',
            ], 403);
        }

        $items = $sale->saleItems->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'product_name' => optional($item->product)->name,
                'quantity' => (int) $item->quantity,
                'price' => (float) $item->price,
                'discount' => (float) $item->discount,
                'subtotal' => (float) $item->subtotal,
            ];
        })->values();

        $payment = $sale->payment ? [
            'id' => $sale->payment->id,
            'method' => $sale->payment->method,
            'amount' => (float) $sale->payment->amount,
            'change_given' => (float) $sale->payment->change_given,
        ] : null;

        $data = [
            'sale' => [
                'id' => $sale->id,
                'status' => $sale->status,
                'payment_method' => $sale->payment_method,
                'total_amount' => (float) $sale->total_amount,
                'created_at' => $sale->created_at?->toISOString(),
            ],
            'items' => $items,
            'payment' => $payment,
            'cashier' => [
                'id' => $sale->cashier->id,
                'name' => $sale->cashier->name,
            ],
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    /**
     * Checkout a sale.
     */
    public function checkout(int $saleId, CheckoutSaleRequest $request): JsonResponse
    {
        $user = auth()->user();

        $sale = Sale::with(['saleItems', 'payment', 'cashier'])->find($saleId);
        if (!$sale) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_SALE_NOT_FOUND',
                'message' => 'Sale not found',
            ], 404);
        }

        if ($sale->cashier_id !== $user->id) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_UNAUTHORIZED',
                'message' => 'You are not authorized to checkout this sale',
            ], 403);
        }

        if ($sale->saleItems->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_EMPTY_SALE',
                'message' => 'Cannot checkout an empty sale',
            ], 422);
        }

        if ($sale->status === 'completed') {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_SALE_ALREADY_COMPLETED',
                'message' => 'Sale has already been completed',
            ], 409);
        }

        $method = $request->payment_method;
        $amount = (float) $request->amount;
        $total = (float) $sale->total_amount;

        // Validate payment sufficient
        if ($amount < $total) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_INSUFFICIENT_PAYMENT',
                'message' => 'Payment amount is less than sale total',
            ], 422);
        }

        $change = 0.0;
        if ($method === 'cash') {
            $change = $amount - $total;
        } else {
            // card: charge exact amount equal/greater; no change given
            $change = 0.0;
        }

        try {
            $completedSale = DB::transaction(function () use ($sale, $method, $amount, $change) {
                // Create payment
                $payment = Payment::create([
                    'sale_id' => $sale->id,
                    'method' => $method,
                    'amount' => $amount,
                    'change_given' => $change,
                ]);

                // Update sale
                $sale->update([
                    'payment_method' => $method,
                    'status' => 'completed',
                ]);

                // Reload with payment details and items
                return $sale->load(['payment', 'saleItems.product']);
            });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'sale' => $completedSale,
                    'message' => 'Sale checked out successfully',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_SALE_CHECKOUT_FAILED',
                'message' => 'Failed to checkout sale',
            ], 500);
        }
    }

    /**
     * List sales with filtering and pagination for reporting/history.
     * Query params:
     * - date (YYYY-MM-DD): filter by sale creation date
     * - cashier_id (admin only): filter by cashier
     * - limit (int): page size, default 10, max 100
     * - offset (int): offset for pagination, default 0
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();

        $limit = (int) ($request->query('limit', 10));
        $offset = (int) ($request->query('offset', 0));
        $limit = $limit > 0 ? min($limit, 100) : 10;
        $offset = max($offset, 0);

        $date = $request->query('date');
        $cashierId = $request->query('cashier_id');

        $query = Sale::query()->with(['cashier', 'payment'])->withCount('saleItems');

        // Role-based access: cashiers only see their own sales
        if ($user->isCashier()) {
            $query->where('cashier_id', $user->id);
        } else {
            // Admins can see all, and filter by cashier_id
            if ($cashierId && $user->isAdmin()) {
                $query->where('cashier_id', (int) $cashierId);
            }
        }

        // Date filter (specific date)
        if ($date) {
            try {
                $d = Carbon::parse($date)->toDateString();
                $query->whereDate('created_at', $d);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'code' => 'ERR_INVALID_DATE',
                    'message' => 'Invalid date format. Use YYYY-MM-DD.',
                ], 422);
            }
        }

        // Summary before pagination
        $total = (clone $query)->count();
        $sumTotalAmount = (float) (clone $query)->sum('total_amount');

        // Apply pagination
        $sales = $query
            ->orderByDesc('created_at')
            ->skip($offset)
            ->take($limit)
            ->get();

        $data = [
            'summary' => [
                'total' => $total,
                'total_amount' => $sumTotalAmount,
            ],
            'pagination' => [
                'limit' => $limit,
                'offset' => $offset,
                'count' => $sales->count(),
            ],
            'sales' => $sales->map(function (Sale $s) {
                return [
                    'id' => $s->id,
                    'status' => $s->status,
                    'payment_method' => $s->payment_method,
                    'total_amount' => (float) $s->total_amount,
                    'items_count' => $s->sale_items_count,
                    'cashier' => [
                        'id' => $s->cashier?->id,
                        'name' => $s->cashier?->name,
                    ],
                    'payment' => $s->payment ? [
                        'id' => $s->payment->id,
                        'method' => $s->payment->method,
                        'amount' => (float) $s->payment->amount,
                        'change_given' => (float) $s->payment->change_given,
                    ] : null,
                    'created_at' => $s->created_at?->toISOString(),
                ];
            })->values(),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    /**
     * Update the quantity of an existing sale item.
     */
    public function updateItemQuantity(int $saleId, int $itemId, Request $request): JsonResponse
    {
        $user = auth()->user();

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $sale = Sale::with(['saleItems', 'saleItems.product'])->find($saleId);
        if (!$sale) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_SALE_NOT_FOUND',
                'message' => 'Sale not found',
            ], 404);
        }

        if ($sale->cashier_id !== $user->id) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_UNAUTHORIZED',
                'message' => 'You are not authorized to modify this sale',
            ], 403);
        }

        if ($sale->status === 'completed') {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_SALE_ALREADY_COMPLETED',
                'message' => 'Sale has already been completed',
            ], 409);
        }

        $saleItem = $sale->saleItems->firstWhere('id', $itemId);
        if (!$saleItem) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_SALE_ITEM_NOT_FOUND',
                'message' => 'Sale item not found',
            ], 404);
        }

        $newQty = (int) $request->quantity;
        $oldQty = (int) $saleItem->quantity;
        if ($newQty === $oldQty) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'sale' => $sale->fresh(['saleItems.product']),
                    'message' => 'Quantity unchanged',
                ],
            ]);
        }

        $product = $saleItem->product; // eager loaded
        if (!$product) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_PRODUCT_NOT_FOUND',
                'message' => 'Product not found for sale item',
            ], 404);
        }

        try {
            $updatedSale = DB::transaction(function () use ($sale, $saleItem, $product, $oldQty, $newQty) {
                $diff = $newQty - $oldQty; // positive if increasing

                // If increasing quantity, ensure enough stock and decrement stock; if decreasing, increment stock back
                if ($diff > 0) {
                    if ($product->stock < $diff) {
                        abort(response()->json([
                            'status' => 'error',
                            'code' => 'ERR_INSUFFICIENT_STOCK',
                            'message' => "Insufficient stock. Available: {$product->stock}, Requested extra: {$diff}",
                        ], 400));
                    }
                    $product->adjustStock($diff, 'out', 'sale', "Sale #{$sale->id} - Item qty increased", null, (string) $sale->id);
                } elseif ($diff < 0) {
                    $product->adjustStock(abs($diff), 'in', 'sale_return', "Sale #{$sale->id} - Item qty decreased", null, (string) $sale->id);
                }

                // Recalculate item subtotal
                $price = (float) $saleItem->price;
                $discount = (float) $saleItem->discount; // Keep same discount absolute amount per item or total? Keep total discount absolute as stored.
                $oldSubtotal = (float) $saleItem->subtotal;
                $gross = $newQty * $price;
                // Ensure discount is not more than gross
                $appliedDiscount = min($discount, $gross);
                $newSubtotal = $gross - $appliedDiscount;

                // Update item
                $saleItem->update([
                    'quantity' => $newQty,
                    'subtotal' => $newSubtotal,
                    // keep price and discount
                ]);

                // Update sale total
                $sale->update([
                    'total_amount' => (float) $sale->total_amount - $oldSubtotal + $newSubtotal,
                ]);

                return $sale->fresh(['saleItems.product']);
            });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'sale' => $updatedSale,
                    'message' => 'Sale item quantity updated successfully',
                ],
            ]);
        } catch (\Symfony\Component\HttpFoundation\Response $abortResponse) {
            return $abortResponse; // forward abort()
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_SALE_ITEM_UPDATE_FAILED',
                'message' => 'Failed to update sale item',
            ], 500);
        }
    }

    /**
     * Remove an item from a sale.
     */
    public function removeItem(int $saleId, int $itemId): JsonResponse
    {
        $user = auth()->user();

        $sale = Sale::with(['saleItems', 'saleItems.product'])->find($saleId);
        if (!$sale) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_SALE_NOT_FOUND',
                'message' => 'Sale not found',
            ], 404);
        }

        if ($sale->cashier_id !== $user->id) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_UNAUTHORIZED',
                'message' => 'You are not authorized to modify this sale',
            ], 403);
        }

        if ($sale->status === 'completed') {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_SALE_ALREADY_COMPLETED',
                'message' => 'Sale has already been completed',
            ], 409);
        }

        $saleItem = $sale->saleItems->firstWhere('id', $itemId);
        if (!$saleItem) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_SALE_ITEM_NOT_FOUND',
                'message' => 'Sale item not found',
            ], 404);
        }

        try {
            $updatedSale = DB::transaction(function () use ($sale, $saleItem) {
                $product = $saleItem->product; // eager loaded
                $qty = (int) $saleItem->quantity;
                $subtotal = (float) $saleItem->subtotal;

                // Return stock back in
                if ($product) {
                    $product->adjustStock($qty, 'in', 'sale_return', "Sale #{$sale->id} - Item removed", null, (string) $sale->id);
                }

                // Update sale total
                $sale->update([
                    'total_amount' => max(0, (float) $sale->total_amount - $subtotal),
                ]);

                // Delete item
                $saleItem->delete();

                return $sale->fresh(['saleItems.product']);
            });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'sale' => $updatedSale,
                    'message' => 'Sale item removed successfully',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_SALE_ITEM_REMOVE_FAILED',
                'message' => 'Failed to remove sale item',
            ], 500);
        }
    }
}
