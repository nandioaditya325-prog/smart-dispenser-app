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
        // 1. Validasi input dasar dari ESP32
        $request->validate([
            'rfid_uid'  => 'required',
            'volume_ml' => 'nullable|numeric',
        ]);

        // 2. Ambil data dari request
        $rfid = $request->input('rfid_uid');
        $volume = $request->input('volume_ml', 0);
        $now = now();

        // 3. Simpan data (hanya masukkan kolom dasar yang aman)
        // Jika tabel kamu punya 'final_volume_ml' atau 'volume_ml', sesuaikan di bawah
        $dataToInsert = [
            'rfid_uid'   => $rfid,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        // Tambahkan volume jika ada nilainya
        if ($volume > 0) {
            // Catatan: ganti 'final_volume_ml' jadi 'volume_ml' jika nama kolom di migration kamu volume_ml
            $dataToInsert['final_volume_ml'] = $volume; 
        }

        $stored = DB::table('transactions')->insert($dataToInsert);

        // 4. Response ke ESP32
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