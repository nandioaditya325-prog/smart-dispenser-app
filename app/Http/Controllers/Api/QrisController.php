<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QrisController extends Controller
{
    /**
     * Mengembalikan data string QRIS statis untuk ditampilkan di LCD ESP32
     */
    public function info()
    {
        return response()->json([
            'status' => 'success',
            'merchant' => 'Depot Air Minum',
            'qr_string' => '00020101021126570011ID.CO.QRIS.WWW01189360091400000000005204581253033605802ID5913SMART DISPENSER6007SEMARANG61055011762070703A0163041A2B'
        ]);
    }

    /**
     * Cek apakah ada transaksi lunas yang harus dituangkan
     */
    public function checkStatus()
    {
        return response()->json([
            'dispense' => false,
            'id' => 0
        ]);
    }

    /**
     * Menerima konfirmasi bahwa penuangan air telah selesai
     */
    public function complete(Request $request)
    {
        return response()->json([
            'status' => 'completed',
            'message' => 'Penuangan selesai'
        ]);
    }
}