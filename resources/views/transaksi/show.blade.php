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
            <div class="row-actions">
                <a class="secondary-button" href="{{ route('transaksi.struk', $transaksi) }}" target="_blank">Print Struk</a>
                <a class="primary-button" href="{{ route('transaksi.create') }}">Transaksi Baru</a>
            </div>
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
            <div>
                <span>Total Modal</span>
                <strong>Rp {{ number_format($transaksi->items->sum('subtotal_modal'), 0, ',', '.') }}</strong>
            </div>
            <div>
                <span>Laba Kotor</span>
                <strong>Rp {{ number_format($transaksi->items->sum('laba_kotor'), 0, ',', '.') }}</strong>
            </div>
        </section>

        <section class="panel table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th class="number">Harga</th>
                        <th class="number">Modal</th>
                        <th class="number">Qty</th>
                        <th class="number">Subtotal</th>
                        <th class="number">Laba Kotor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transaksi->items as $item)
                        <tr>
                            <td>{{ $item->kode_barang }}</td>
                            <td>{{ $item->nama_barang }}</td>
                            <td class="number">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                            <td class="number">Rp {{ number_format($item->harga_modal, 0, ',', '.') }}</td>
                            <td class="number">{{ rtrim(rtrim(number_format($item->qty, 3, ',', '.'), '0'), ',') }}</td>
                            <td class="number">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            <td class="number">Rp {{ number_format($item->laba_kotor, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
