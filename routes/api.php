<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TransactionController; // Panggil controllernya

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Di sinilah rute API proyek kamu didaftarkan. Rute ini dimuat oleh
| RouteServiceProvider dan semuanya akan diberi prefiks "api".
|
*/

// Rute untuk menerima data transaksi baru dari ESP32
// URL-nya akan menjadi: http://127.0.0.1:8000/api/transactions
Route::post('/transactions', [TransactionController::class, 'store']);