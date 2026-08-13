<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    // 1. Menyimpan transaksi baru (dari ESP32 / QRIS / Coin Acceptor)
    public function store(Request $request)
    {
        $rfid = $request->input('rfid_uid', 'TRX-' . time());
        $volume = $request->input('volume_ml', 600);
        $waterType = $request->input('water_type', 'normal');
        $price = $request->input('price', 1000);
        $paymentStatus = $request->input('payment_status', 'pending');

        $id = DB::table('transactions')->insertGetId([
            'rfid_uid'       => $rfid,
            'volume_ml'      => $volume,
            'water_type'     => $waterType,
            'price'          => $price,
            'payment_status' => $paymentStatus,
            'success'        => 0,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        if ($id) {
            return response()->json([
                'status'  => 'ok',
                'message' => 'Transaksi tersimpan',
                'data'    => [
                    'id'         => $id,
                    'rfid_uid'   => $rfid,
                    'price'      => $price,
                    'volume_ml'  => $volume,
                ]
            ], 201);
        }

        return response()->json(['status' => 'error'], 500);
    }

    // 2. Diperiksa berkala oleh ESP32 untuk cek transaksi QRIS yang bernilai 'paid'
    public function checkStatus()
    {
        $pending = DB::table('transactions')
            ->where('payment_status', 'paid')
            ->where('success', 0)
            ->latest()
            ->first();

        if ($pending) {
            return response()->json([
                'status'     => 'paid',
                'dispense'   => true,
                'id'         => $pending->id,
                'water_type' => $pending->water_type,
                'volume_ml'  => $pending->volume_ml
            ], 200);
        }

        return response()->json([
            'status'   => 'waiting',
            'dispense' => false
        ], 200);
    }

    // 3. Konfirmasi dari ESP32 bahwa penuangan air telah selesai
    public function completeTransaction(Request $request)
    {
        $id = $request->input('id');

        $updated = DB::table('transactions')
            ->where('id', $id)
            ->update([
                'success'      => 1,
                'completed_at' => now(),
                'updated_at'   => now()
            ]);

        if ($updated) {
            return response()->json(['status' => 'completed'], 200);
        }

        return response()->json(['status' => 'error'], 400);
    }

    // 4. Webhook callback dari server InterActive QRIS (Aman dari Bayar Kurang)
    public function handleCallback(Request $request)
    {
        $invoice = $request->input('invoice_id');
        $status = $request->input('status');
        $amountPaid = (int) $request->input('amount', 1000); // Nominal transfer dari pembeli

        // Cari transaksi yang statusnya pending
        $transaction = DB::table('transactions')
            ->where('payment_status', 'pending')
            ->latest()
            ->first();

        if ($transaction) {
            // SYARAT: Status PAID DAN Uang Pembayaran TIDAK BOLEH KURANG
            if (($status === 'PAID' || $status === 'SUCCESS') && $amountPaid >= $transaction->price) {
                DB::table('transactions')
                    ->where('id', $transaction->id)
                    ->update([
                        'payment_status' => 'paid',
                        'updated_at'     => now()
                    ]);
                
                return response()->json(['status' => 'ok', 'message' => 'Lunas!'], 200);
            } else {
                // Jika bayar kurang (misal Rp 1), tandai underpaid biar AIR TIDAK KELUAR
                DB::table('transactions')
                    ->where('id', $transaction->id)
                    ->update([
                        'payment_status' => 'underpaid',
                        'updated_at'     => now()
                    ]);

                return response()->json(['status' => 'failed', 'message' => 'Nominal Kurang!'], 400);
            }
        }

        return response()->json(['status' => 'ignored'], 404);
    }

    // 5. Mengirim data String QRIS Statis asli dari PDF ke ESP32 / LCD
    public function getQrisInfo()
    {
        return response()->json([
            'status'    => 'success',
            'merchant'  => 'DEPOT AIR MINUM SMART WATER IOT',
            'nmid'      => 'ID1026567767466',
            'qr_string' => '00020101021126670016ID.CO.INTERACTIVE.WWW01189360081510265677674660215ID10265677674660303UMI51440014ID.CO.QRIS.WWW0215ID10265677674660303UMI5204599953033605802ID5928DEPOT AIR MINUM SMART WAT6013SEMARANG61055011162070703A0163045E7A',
            'price'     => 1000
        ], 200);
    }
}