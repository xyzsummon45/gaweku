<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi</title>
    @include('transaksi.styles')
</head>
<body>
    @include('layouts.navbar')

    <main class="page">
        <header class="page-header">
            <div>
                <p>{{ $transaksi->kode_transaksi }}</p>
                <h1>Detail Transaksi</h1>
            </div>
            <a class="primary-button" href="{{ route('transaksi.create') }}">Transaksi Baru</a>
        </header>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <section class="panel summary-grid">
            <div>
                <span>Kode Transaksi</span>
                <strong>{{ $transaksi->kode_transaksi }}</strong>
            </div>
            <div>
                <span>Tanggal</span>
                <strong>{{ $transaksi->tanggal->format('d/m/Y H:i') }}</strong>
            </div>
            <div>
                <span>Total</span>
                <strong>Rp {{ number_format($transaksi->total, 0, ',', '.') }}</strong>
            </div>
        </section>

        <section class="panel table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th class="number">Harga</th>
                        <th class="number">Qty</th>
                        <th class="number">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transaksi->items as $item)
                        <tr>
                            <td>{{ $item->kode_barang }}</td>
                            <td>{{ $item->nama_barang }}</td>
                            <td class="number">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                            <td class="number">{{ $item->qty }}</td>
                            <td class="number">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
