<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SalesController;
use App\Http\Controllers\Api\CategoryController;
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
        'index', 'show', 'update', 'destroy'
    ]);
    
    // Additional user routes if needed
    Route::get('users/{id}/profile', [UserController::class, 'show']);
});

// Product, Category & Inventory Management (Manager, Admin only)
Route::middleware('auth:api')->group(function () {
    // Product CRUD routes
    Route::apiResource('products', ProductController::class);

    // Category routes
    Route::get('categories', [CategoryController::class, 'index']);
    
    // Inventory management routes
    Route::prefix('inventory')->group(function () {
        Route::post('add', [InventoryController::class, 'addStock']);
        Route::post('remove', [InventoryController::class, 'removeStock']);
        Route::get('movements', [InventoryController::class, 'getAllStockMovements']);
        Route::get('products/{productId}/movements', [InventoryController::class, 'getProductStockMovements']);
        Route::get('low-stock', [InventoryController::class, 'getLowStockProducts']);
    });
});

// Future routes for POS system modules
Route::middleware('auth:api')->group(function () {
    // Sales routes (Cashier, Manager, Admin)
    Route::get('sales', [SalesController::class, 'index']);
    Route::post('sales', [SalesController::class, 'store']);
    Route::post('sales/{saleId}/items', [SalesController::class, 'addItem']);
    Route::patch('sales/{saleId}/checkout', [SalesController::class, 'checkout']);
    Route::get('sales/{saleId}', [SalesController::class, 'show']);
    Route::patch('sales/{saleId}/items/{itemId}', [SalesController::class, 'updateItemQuantity']);
    Route::delete('sales/{saleId}/items/{itemId}', [SalesController::class, 'removeItem']);
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
