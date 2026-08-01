<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\DashboardController;

Route::get('/', [DashboardController::class, 'index']);

// Penanganan jika halaman /checkout diakses via GET (diketik langsung / di-refresh di HP)
Route::get('/checkout', function () {
    return redirect('/');
});

Route::post('/checkout', [DashboardController::class, 'checkout'])->name('checkout');
Route::get('/payment/{id}', [DashboardController::class, 'payment'])->name('payment');
Route::post('/payment-success/{id}', [DashboardController::class, 'paySuccess'])->name('pay.success');