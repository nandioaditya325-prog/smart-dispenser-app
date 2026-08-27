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

        // Cek dan buat kolom rfid_uid otomatis jika belum ada di PostgreSQL Railway
        if (!Schema::hasColumn('transactions', 'rfid_uid')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->string('rfid_uid')->nullable();
            });
        }

        $transaction = Transaction::create([
            'order_id'         => 'ORD-' . time() . '-' . rand(100, 999),
            'device_id'        => null,
            'rfid_uid'         => 'QRIS-' . rand(100, 999),
            'qr_code_id'       => null,
            'water_type'       => $request->water_type,
            'volume_ml'        => $volume,
            'target_volume_ml' => $volume,
            'final_volume_ml'  => 0,
            'amount'           => $price,
            'price'            => $price,
            'status'           => 'pending',
            'payment_status'   => 'pending',
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
        $transaction->update([
            'payment_status' => 'paid',
            'status'         => 'success',
        ]);

        return redirect('/')->with('success', 'Pembayaran QRIS Berhasil! Silahkan ambil air Anda.');
    }
}