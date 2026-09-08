<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produksi</title>
    @include('barang.styles')
</head>
<body>
    @include('layouts.navbar')

    <main class="page">
        <header class="page-header">
            <div>
                <p>{{ $produksi->kode_produksi }}</p>
                <h1>Detail Produksi</h1>
            </div>
            <a class="primary-button" href="{{ route('produksi.create') }}">Produksi Baru</a>
        </header>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <section class="panel summary-grid">
            <div>
                <span>Formula</span>
                <strong>{{ $produksi->nama_formula }}</strong>
            </div>
            <div>
                <span>Barang Hasil</span>
                <strong>{{ $produksi->barangHasil->nama_barang }}</strong>
            </div>
            <div>
                <span>Qty Produksi</span>
                <strong>{{ rtrim(rtrim(number_format($produksi->qty_produksi, 3, ',', '.'), '0'), ',') }} {{ $produksi->satuan_hasil }}</strong>
            </div>
            <div>
                <span>Total Biaya</span>
                <strong>Rp {{ number_format($produksi->total_biaya, 0, ',', '.') }}</strong>
            </div>
            <div>
                <span>HPP</span>
                <strong>Rp {{ number_format($produksi->hpp, 0, ',', '.') }}</strong>
            </div>
            <div>
                <span>Tanggal</span>
                <strong>{{ $produksi->tanggal->format('d/m/Y H:i') }}</strong>
            </div>
        </section>

        @if ($produksi->catatan)
            <section class="panel">
                <strong>Catatan</strong>
                <p>{{ $produksi->catatan }}</p>
            </section>
        @endif

        <section class="panel table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Bahan</th>
                        <th class="number">Qty Formula</th>
                        <th class="number">Qty Pakai</th>
                        <th>Satuan</th>
                        <th class="number">Harga Beli</th>
                        <th class="number">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($produksi->items as $item)
                        <tr>
                            <td>{{ $item->kode_barang }}</td>
                            <td>{{ $item->nama_barang }}</td>
                            <td class="number">{{ rtrim(rtrim(number_format($item->qty_formula, 3, ',', '.'), '0'), ',') }}</td>
                            <td class="number">{{ rtrim(rtrim(number_format($item->qty_pakai, 3, ',', '.'), '0'), ',') }}</td>
                            <td>{{ $item->satuan }}</td>
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
