<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->role->name === 'Admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role->name === 'Manager') {
            return redirect()->route('manager.dashboard');
        } else {
            return redirect('/login');
        }
    }
    return view('auth.login');
});


// Admin login route
// Route::get('/login', function () {
//     return view('admin.auth.login');
// })->name('login');
Route::get('/login', function () {
    return view('auth.login');
})->name('login');



// Admin routes (protected)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/users', function () {
        return view('admin.users');
    })->name('users');

    Route::get('/system-config', function () {
        return view('admin.system-config');
    })->name('system-config');

    Route::get('/reports-analytics', function () {
        return view('admin.reports-analytics');
    })->name('reports-analytics');

    Route::get('/audit', function () {
        return view('admin.audit');
    })->name('audit');
});

// Manager login route
Route::get('/manager/login', function () {
    return view('manager.auth.login');
})->name('manager.login');

Route::prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', function () {
        return view('manager.dashboard');
    })->name('dashboard');

    Route::get('/products', function () {
        return view('manager.products');
    })->name('products');

    Route::get('/inventory', function () {
        return view('manager.inventory');
    })->name('inventory');

    Route::get('/supplier', function () {
        return view('manager.supplier');
    })->name('supplier');

    Route::get('/discounts', function () {
        return view('manager.discounts');
    })->name('discounts');

    Route::get('/reports', function () {
        return view('manager.reports');
    })->name('reports');
});
