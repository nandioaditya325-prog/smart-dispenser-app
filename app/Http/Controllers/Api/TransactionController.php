<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Menerima data transaksi baru dari ESP32
     */
    public function store(Request $request)
    {
        // 1. Ambil data dari ESP32
        $rfid = $request->input('rfid_uid');
        $volume = $request->input('volume_ml', 0);
        $now = now();

        // 2. Simpan data ke tabel 'transactions' di database
        $stored = DB::table('transactions')->insert([
            'rfid_uid'        => $rfid,
            'final_volume_ml' => $volume,
            'status'          => 'success',
            'success'         => true,
            'completed_at'    => $now,
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);

        // 3. Cek apakah berhasil simpan
        if ($stored) {
            return response()->json([
                'status'  => 'ok',
                'message' => 'Transaksi tersimpan',
                'data'    => [
                    'rfid_uid'  => $rfid,
                    'volume_ml' => $volume
                ]
            ], 201);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Gagal menyimpan transaksi'
        ], 500);
    }
}