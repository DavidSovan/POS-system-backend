<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * List all categories (active only).
     */
    public function index(): JsonResponse
    {
        $categories = Category::where('status', 'active')->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories,
        ]);
    }
}
