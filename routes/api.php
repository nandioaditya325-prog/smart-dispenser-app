<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\QrisController; // <-- Pakai QrisController

// 1. ESP32 minta data QRIS & Nama Merchant untuk ditampilkan di LCD
Route::get('/qris/info', [QrisController::class, 'info']);

// 2. InterActive QRIS mengirim sinyal notifikasi saat ada uang masuk
Route::post('/qris/callback', [QrisController::class, 'complete']);

// 3. ESP32 bertanya ke server: "Apakah uang Rp 1.000 sudah masuk?" (Polling)
Route::get('/qris/check-status', [QrisController::class, 'checkStatus']);

// 4. ESP32 memberi kabar ke server kalau penuangan air sudah selesai
Route::post('/qris/complete', [QrisController::class, 'complete']);

// 5. Route bantuan untuk jalankan migrasi & clear cache
Route::get('/run-migrate', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    return response()->json(['status' => 'Migration Success!']);
});

Route::get('/clear-cache', function () {
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    return response()->json(['status' => 'Cache Cleared Successfully!']);
});