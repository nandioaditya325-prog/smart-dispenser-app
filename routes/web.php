<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\DashboardController;

Route::get('/', [DashboardController::class, 'index']);
Route::post('/checkout', [DashboardController::class, 'checkout'])->name('checkout');
Route::get('/payment/{id}', [DashboardController::class, 'payment'])->name('payment');
Route::post('/payment-success/{id}', [DashboardController::class, 'paySuccess'])->name('pay.success');