public function store(Request $request)
{
    $request->validate([
        'rfid_uid'  => 'required',
        'volume_ml' => 'nullable|numeric',
    ]);

    $rfid = $request->input('rfid_uid');
    $volume = $request->input('volume_ml', 0);
    $now = now();

    // Hapus 'status' dari array insert
    $stored = DB::table('transactions')->insert([
        'rfid_uid'   => $rfid,
        'volume_ml'  => $volume,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

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

    return response()->json(['status' => 'error'], 500);
}