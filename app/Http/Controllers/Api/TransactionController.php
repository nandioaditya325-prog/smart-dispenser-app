<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Untuk pakai Query Builder

class TransactionController extends Controller
{
    /**
     * Menerima data transaksi baru dari ESP32
     */
    public function store(Request $request)
    {
        // 1. Ambil data dari ESP32
        $rfid = $request->input('rfid_uid'); // UID Kartu
        $volume = $request->input('volume_ml'); // Volume air

        // 2. Simpan data ke tabel 'transactions' di database
        // Kita pakai Query Builder biar simpel dulu
        $stored = DB::table('transactions')->insert([
            'rfid_uid' => $rfid,
            'volume_ml' => $volume,
            'status' => 'success', // Kita anggap sukses dulu
            // 'created_at' otomatis terisi oleh database
        ]);

        // 3. Cek apakah berhasil simpan
        if ($stored) {
            // Jika berhasil, beri respon balik ke ESP32
            return response()->json([
                'status' => 'ok',
                'message' => 'Transaksi tersimpan',
                'data' => [
                    'rfid_uid' => $rfid,
                    'volume_ml' => $volume
                ]
            ], 201); // 201 Created
        } else {
            // Jika gagal
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan transaksi'
            ], 500); // 500 Internal Server Error
        }
    }
}