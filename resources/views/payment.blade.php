<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran QRIS - Smart Dispenser</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5 text-center">
        <div class="card mx-auto shadow-sm" style="max-width: 450px;">
            <div class="card-body">
                <h4 class="fw-bold">Scan QRIS Untuk Membayar</h4>
                <p class="text-muted">Dispenser Air {{ strtoupper($transaction->water_type) }} ({{ $transaction->volume_ml }} ml)</p>
                
                <div class="my-3">
                    <!-- QR Code Dummy menggunakan API Gratis -->
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=SIMULASI_QRIS_PAYMENT_{{ $transaction->id }}" alt="QRIS Code" class="img-fluid border p-2">
                </div>

                <h3 class="fw-bold text-success mb-3">Rp {{ number_format($transaction->price) }}</h3>

                <!-- Tombol Simulasi Lunas -->
                <form action="{{ route('pay.success', $transaction->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-lg w-100">
                        [SIMULASI] Saya Sudah Bayar
                    </button>
                </form>

                <!-- PERBAIKAN DI SINI: Menggunakan url('/') agar mengarah ke subfolder proyek -->
                <a href="{{ url('/') }}" class="btn btn-link mt-2 text-decoration-none text-muted">Batal</a>
            </div>
        </div>
    </div>
</body>
</html>