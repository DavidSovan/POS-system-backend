<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\DiscountController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
    Route::post('register', [AuthController::class, 'register']); // Admin only - checked in controller
});

// User management routes (Protected)
Route::middleware('auth:api')->group(function () {
    Route::apiResource('users', UserController::class)->only([
        'index',
        'show',
        'update',
        'destroy'
    ]);

    // Additional user routes if needed
    Route::get('users/{id}/profile', [UserController::class, 'show']);
});

// Product & Inventory Management (Manager, Admin only)
Route::middleware('auth:api')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index']);

    // Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // Inventory
    Route::post('/inventory/add', [InventoryController::class, 'addStock']);
    Route::post('/inventory/remove', [InventoryController::class, 'removeStock']);
    Route::get('/inventory/{id}/movements', [InventoryController::class, 'getProductStockMovements']);
    Route::get('/inventory/movements', [InventoryController::class, 'getAllStockMovements']);
    Route::get('/inventory/low-stock', [InventoryController::class, 'getLowStockProducts']);
});

// Supplier Management (Manager, Admin only)
Route::middleware('auth:api')->group(function () {
    // Supplier Management
    Route::get('/suppliers', [SupplierController::class, 'index']);
    Route::post('/suppliers', [SupplierController::class, 'store']);
    Route::get('/suppliers/{id}', [SupplierController::class, 'show']);
    Route::put('/suppliers/{id}', [SupplierController::class, 'update']);
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy']);
});

// Discount Management (Cashier, Manager, Admin)
Route::middleware('auth:api')->group(function () {
    Route::get('/discounts', [DiscountController::class, 'index']);
    Route::post('/discounts', [DiscountController::class, 'store']);
    Route::get('/discounts/{id}', [DiscountController::class, 'show']);
    Route::put('/discounts/{id}', [DiscountController::class, 'update']);
    Route::delete('/discounts/{id}', [DiscountController::class, 'destroy']);
});

// Future routes for POS system modules
Route::middleware('auth:api')->group(function () {
    // Sales routes (Cashier, Manager, Admin)
    // Route::apiResource('sales', SalesController::class);

    // Reports routes (Manager, Admin only)
    // Route::prefix('reports')->group(function () {
    //     Route::get('sales', [ReportController::class, 'sales']);
    //     Route::get('inventory', [ReportController::class, 'inventory']);
    // });

    // System configuration routes (Admin only)
    // Route::prefix('system')->group(function () {
    //     Route::get('config', [SystemController::class, 'getConfig']);
    //     Route::post('config', [SystemController::class, 'updateConfig']);
    // });
});
