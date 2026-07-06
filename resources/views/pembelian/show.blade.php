<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pembelian</title>
    @include('barang.styles')
</head>
<body>
    @include('layouts.navbar')

    <main class="page">
        <header class="page-header">
            <div>
                <p>{{ $pembelian->kode_pembelian }}</p>
                <h1>Detail Pembelian</h1>
            </div>
            <a class="primary-button" href="{{ route('pembelian.create') }}">Pembelian Baru</a>
        </header>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <section class="panel summary-grid">
            <div>
                <span>Supplier</span>
                <strong>{{ $pembelian->supplier->nama_supplier }}</strong>
            </div>
            <div>
                <span>Nomor Invoice</span>
                <strong>{{ $pembelian->nomor_invoice }}</strong>
            </div>
            <div>
                <span>Jatuh Tempo</span>
                <strong>{{ $pembelian->tanggal_jatuh_tempo->format('d/m/Y') }}</strong>
            </div>
            <div>
                <span>Status</span>
                <strong>{{ str_replace('_', ' ', ucfirst($pembelian->status_pembayaran)) }}</strong>
            </div>
            <div>
                <span>Total Hutang</span>
                <strong>Rp {{ number_format($pembelian->total, 0, ',', '.') }}</strong>
            </div>
        </section>

        @if ($pembelian->catatan)
            <section class="panel">
                <strong>Catatan</strong>
                <p>{{ $pembelian->catatan }}</p>
            </section>
        @endif

        <section class="panel table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th class="number">Qty</th>
                        <th class="number">Harga Beli</th>
                        <th class="number">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pembelian->items as $item)
                        <tr>
                            <td>{{ $item->kode_barang }}</td>
                            <td>{{ $item->nama_barang }}</td>
                            <td class="number">{{ rtrim(rtrim(number_format($item->qty, 3, ',', '.'), '0'), ',') }}</td>
                            <td class="number">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                            <td class="number">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
