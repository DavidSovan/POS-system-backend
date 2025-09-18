<?php

// app/Http/Controllers/Api/DiscountController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::with(['user', 'product'])->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'discounts' => $discounts
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'percentage' => 'required|numeric|min:0|max:100',
            'reason' => 'nullable|string',
        ]);

        $discount = Discount::create(array_merge($validated, [
            'user_id' => auth()->id(),
            'status' => 'Pending',
        ]));

        return response()->json([
            'status' => 'success',
            'data' => ['discount' => $discount],
        ], 201);
    }

    public function show($id)
    {
        $discount = Discount::with(['user', 'product'])->find($id);
        if (!$discount) {
            return response()->json(['status' => 'error', 'message' => 'Discount not found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => ['discount' => $discount]]);
    }

    public function update(Request $request, $id)
    {
        $discount = Discount::find($id);
        if (!$discount) {
            return response()->json(['status' => 'error', 'message' => 'Discount not found'], 404);
        }

        $validated = $request->validate([
            'percentage' => 'sometimes|numeric|min:0|max:100',
            'reason' => 'nullable|string',
            'status' => 'sometimes|in:Pending,Approved,Rejected'
        ]);

        $discount->update($validated);

        return response()->json(['status' => 'success', 'data' => ['discount' => $discount]]);
    }

    public function destroy($id)
    {
        $discount = Discount::find($id);
        if (!$discount) {
            return response()->json(['status' => 'error', 'message' => 'Discount not found'], 404);
        }

        $discount->delete();

        return response()->json(['status' => 'success', 'message' => 'Discount deleted successfully']);
    }
}
