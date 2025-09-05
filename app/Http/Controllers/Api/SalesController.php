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
     * Checkout a sale.
     */
    public function checkout(int $saleId, CheckoutSaleRequest $request): JsonResponse
    {
        $user = auth()->user();

        $sale = Sale::with(['saleItems', 'payment'])->find($saleId);
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
}
