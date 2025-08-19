<?php

use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/healthcheck', function() {
    try {
        return [
            'status' => 'active',
            'database' => Test::exists() ? 'connected' : 'no_records'
        ];
    } catch (\Exception $e) {
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
});