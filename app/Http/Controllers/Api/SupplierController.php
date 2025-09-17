<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of suppliers.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Supplier::query();

        // 🔹 Filtering
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%");
            });
        }

        // 🔹 Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        if (in_array($sortBy, ['name', 'email', 'phone', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        }

        // 🔹 Pagination
        $perPage = min($request->get('per_page', 15), 100);
        $suppliers = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => [
                'suppliers' => $suppliers->items(),
                'pagination' => [
                    'current_page' => $suppliers->currentPage(),
                    'last_page' => $suppliers->lastPage(),
                    'per_page' => $suppliers->perPage(),
                    'total' => $suppliers->total(),
                ],
            ],
        ]);
    }

    /**
     * Store a newly created supplier.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'nullable|email|max:255',
            'phone'          => 'nullable|string|max:50',
            'address'        => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'status'         => 'in:active,inactive',
        ]);

        try {
            $supplier = Supplier::create($validated);

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'supplier' => $supplier,
                    'message'  => 'Supplier created successfully',
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 'ERR_SUPPLIER_CREATION_FAILED',
                'message' => 'Failed to create supplier',
            ], 500);
        }
    }

    /**
     * Display the specified supplier.
     */
    public function show($id): JsonResponse
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json([
                'status' => 'error',
                'code'   => 'ERR_SUPPLIER_NOT_FOUND',
                'message' => 'Supplier not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'supplier' => $supplier,
            ],
        ]);
    }

    /**
     * Update the specified supplier.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json([
                'status' => 'error',
                'code'   => 'ERR_SUPPLIER_NOT_FOUND',
                'message' => 'Supplier not found',
            ], 404);
        }

        $validated = $request->validate([
            'name'           => 'sometimes|required|string|max:255',
            'email'          => 'nullable|email|max:255',
            'phone'          => 'nullable|string|max:50',
            'address'        => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'status'         => 'in:active,inactive',
        ]);

        try {
            $supplier->update($validated);

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'supplier' => $supplier,
                    'message'  => 'Supplier updated successfully',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 'ERR_SUPPLIER_UPDATE_FAILED',
                'message' => 'Failed to update supplier',
            ], 500);
        }
    }

    /**
     * Remove the specified supplier.
     */
    public function destroy($id): JsonResponse
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json([
                'status' => 'error',
                'code'   => 'ERR_SUPPLIER_NOT_FOUND',
                'message' => 'Supplier not found',
            ], 404);
        }

        try {
            $supplier->delete();

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'message' => 'Supplier deleted successfully',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 'ERR_SUPPLIER_DELETE_FAILED',
                'message' => 'Failed to delete supplier',
            ], 500);
        }
    }
}
