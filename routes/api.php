<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\QrisController;

/*
|--------------------------------------------------------------------------
| API Routes for Smart Dispenser ESP32
|--------------------------------------------------------------------------
*/

Route::get('/qris/info', [QrisController::class, 'info']);
Route::get('/qris/check-status', [QrisController::class, 'checkStatus']);
Route::post('/qris/complete', [QrisController::class, 'complete']);
Route::post('/qris/callback', [QrisController::class, 'complete']);

// Route Pembersih Cache Server Railway
Route::get('/clear-cache', function () {
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    return response()->json(['status' => 'Cache Cleared Successfully!']);
});