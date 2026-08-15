<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Web\DashboardController;

// Auto-run migration secara otomatis saat aplikasi diakses di Railway
try {
    Artisan::call('migrate', ['--force' => true]);
} catch (\Throwable $e) {
    // Abaikan error jika migrasi sudah berjalan
}

Route::get('/', [DashboardController::class, 'index']);

// --- INI WAJIB ADA ---
// Menangani jika diakses langsung lewat address bar di HP (GET)
Route::get('/checkout', function () {
    return redirect('/');
});
// ---------------------

Route::post('/checkout', [DashboardController::class, 'checkout'])->name('checkout');
Route::get('/payment/{id}', [DashboardController::class, 'payment'])->name('payment');
Route::post('/payment-success/{id}', [DashboardController::class, 'paySuccess'])->name('pay.success');