<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        $rfid = $request->input('rfid_uid');
        $volume = $request->input('volume_ml');

        // Masukkan HANYA rfid_uid dan volume_ml (tanpa kolom status)
        $stored = DB::table('transactions')->insert([
            'rfid_uid'  => $rfid,
            'volume_ml' => $volume,
        ]);

        if ($stored) {
            return response()->json([
                'status'  => 'ok',
                'message' => 'Transaksi tersimpan',
                'data'    => [
                    'rfid_uid'  => $rfid,
                    'volume_ml' => $volume,
                ]
            ], 201);
        }

        return response()->json(['status' => 'error'], 500);
    }
}