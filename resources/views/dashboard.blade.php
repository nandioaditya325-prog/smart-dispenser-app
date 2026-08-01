<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Smart Dispenser IoT - QRIS</title>

    <!-- PWA Manifest & Theme Color -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#198754">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h2 class="text-center fw-bold mb-4">🚰 Smart Dispenser IoT</h2>

        @if(session('success'))
            <div class="alert alert-success text-center font-weight-bold">
                {{ session('success') }}
            </div>
        @endif

        <div class="row g-4 justify-content-center mb-5">
            <!-- Air Normal -->
            <div class="col-md-4">
                <div class="card text-center shadow-sm border-primary">
                    <div class="card-body">
                        <h4 class="card-title text-primary">Air Normal</h4>
                        <p class="fs-5">500 ml</p>
                        <h3 class="fw-bold">Rp 1.000</h3>
                        <form action="{{ route('checkout') }}" method="POST">
                            @csrf
                            <input type="hidden" name="water_type" value="normal">
                            <button type="submit" class="btn btn-primary w-100 mt-2">Beli via QRIS</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Air Panas -->
            <div class="col-md-4">
                <div class="card text-center shadow-sm border-danger">
                    <div class="card-body">
                        <h4 class="card-title text-danger">Air Panas</h4>
                        <p class="fs-5">250 ml</p>
                        <h3 class="fw-bold">Rp 1.000</h3>
                        <form action="{{ route('checkout') }}" method="POST">
                            @csrf
                            <input type="hidden" name="water_type" value="hot">
                            <button type="submit" class="btn btn-danger w-100 mt-2">Beli via QRIS</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Air Dingin -->
            <div class="col-md-4">
                <div class="card text-center shadow-sm border-info">
                    <div class="card-body">
                        <h4 class="card-title text-info">Air Dingin</h4>
                        <p class="fs-5">500 ml</p>
                        <h3 class="fw-bold">Rp 1.500</h3>
                        <form action="{{ route('checkout') }}" method="POST">
                            @csrf
                            <input type="hidden" name="water_type" value="cold">
                            <button type="submit" class="btn btn-info text-white w-100 mt-2">Beli via QRIS</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Transaksi Terakhir -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="m-0 fw-bold">Riwayat Transaksi Terakhir</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Kode Transaksi</th>
                            <th>Tipe Air</th>
                            <th>Volume</th>
                            <th>Harga</th>
                            <th>Status Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestTransactions as $trx)
                        <tr>
                            <td>{{ $trx->created_at->format('H:i:s') }}</td>
                            <td>{{ $trx->rfid_uid }}</td>
                            <td><span class="badge bg-secondary">{{ strtoupper($trx->water_type) }}</span></td>
                            <td>{{ $trx->volume_ml }} ml</td>
                            <td>Rp {{ number_format($trx->price) }}</td>
                            <td>
                                @if($trx->payment_status == 'paid')
                                    <span class="badge bg-success">PAID / LUNAS</span>
                                @else
                                    <span class="badge bg-warning text-dark">PENDING</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada transaksi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>