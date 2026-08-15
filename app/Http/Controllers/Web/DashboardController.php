<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class DashboardController extends Controller
{
    public function index()
    {
        $this->ensureTableExists();
        $latestTransactions = Transaction::latest()->take(5)->get();
        return view('dashboard', compact('latestTransactions'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'water_type' => 'required|in:normal,hot,cold',
        ]);

        if ($request->water_type == 'normal') {
            $volume = 500;
            $price = 1000;
        } elseif ($request->water_type == 'hot') {
            $volume = 250;
            $price = 1000;
        } else {
            $volume = 500;
            $price = 1500;
        }

        // Pastikan tabel transactions beserta kolom rfid_uid selalu siap
        $this->ensureTableExists();

        $transaction = Transaction::create([
            'order_id'       => null,
            'device_id'      => null,
            'rfid_uid'       => 'QRIS-' . rand(100, 999),
            'water_type'     => $request->water_type,
            'volume_ml'      => $volume,
            'price'          => $price,
            'payment_status' => 'pending',
        ]);

        return redirect()->route('payment', $transaction->id);
    }

    public function payment($id)
    {
        $transaction = Transaction::findOrFail($id);
        return view('payment', compact('transaction'));
    }

    public function paySuccess($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->update(['payment_status' => 'paid']);

        return redirect('/')->with('success', 'Pembayaran QRIS Berhasil! Silahkan ambil air Anda.');
    }

    /**
     * Memastikan tabel transactions dan kolom rfid_uid terbuat otomatis di SQLite Railway
     */
    private function ensureTableExists()
    {
        try {
            if (!Schema::hasTable('transactions')) {
                Schema::create('transactions', function (Blueprint $table) {
                    $table->id();
                    $table->string('order_id')->nullable();
                    $table->string('device_id')->nullable();
                    $table->string('rfid_uid')->nullable();
                    $table->string('water_type');
                    $table->integer('volume_ml');
                    $table->integer('price');
                    $table->string('payment_status')->default('pending');
                    $table->timestamps();
                });
            } else if (!Schema::hasColumn('transactions', 'rfid_uid')) {
                Schema::table('transactions', function (Blueprint $table) {
                    $table->string('rfid_uid')->nullable();
                });
            }
        } catch (\Throwable $e) {
            // Abaikan jika sudah dibuat secara bersamaan
        }
    }
}