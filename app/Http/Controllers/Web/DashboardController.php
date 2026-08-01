<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

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
}