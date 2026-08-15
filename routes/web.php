<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Http\Controllers\Web\DashboardController;

// Paksa tambahkan kolom rfid_uid langsung ke tabel SQLite di Railway
try {
    if (Schema::hasTable('transactions') && !Schema::hasColumn('transactions', 'rfid_uid')) {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('rfid_uid')->nullable();
        });
    }
} catch (\Throwable $e) {
    // Abaikan jika sudah ada
}

Route::get('/', [DashboardController::class, 'index']);

// --- INI WAJIB ADA ---
Route::get('/checkout', function () {
    return redirect('/');
});
// ---------------------

Route::post('/checkout', [DashboardController::class, 'checkout'])->name('checkout');
Route::get('/payment/{id}', [DashboardController::class, 'payment'])->name('payment');
Route::post('/payment-success/{id}', [DashboardController::class, 'paySuccess'])->name('pay.success');