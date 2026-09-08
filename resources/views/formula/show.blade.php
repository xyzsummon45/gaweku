<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Formula</title>
    @include('barang.styles')
</head>
<body>
    @include('layouts.navbar')

    <main class="page">
        <header class="page-header">
            <div>
                <p>{{ $formula->barangJadi->nama_barang }}</p>
                <h1>{{ $formula->nama_formula }}</h1>
            </div>
            <div class="header-actions">
                <a class="secondary-button" href="{{ route('formula.edit', $formula) }}">Edit Formula</a>
                <a class="primary-button" href="{{ route('formula.create') }}">Tambah Formula</a>
            </div>
        </header>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <section class="panel summary-grid">
            <div>
                <span>Produk Jadi</span>
                <strong>{{ $formula->barangJadi->nama_barang }}</strong>
            </div>
            <div>
                <span>Qty Hasil</span>
                <strong>{{ rtrim(rtrim(number_format($formula->qty_hasil, 3, ',', '.'), '0'), ',') }} {{ $formula->barangJadi->satuan }}</strong>
            </div>
            <div>
                <span>Status</span>
                <strong>{{ $formula->aktif ? 'Aktif' : 'Nonaktif' }}</strong>
            </div>
            <div>
                <span>Total Biaya Formula</span>
                <strong>Rp {{ number_format($formula->total_biaya, 0, ',', '.') }}</strong>
            </div>
            <div>
                <span>HPP per {{ $formula->barangJadi->satuan }}</span>
                <strong>Rp {{ number_format($formula->hpp, 0, ',', '.') }}</strong>
            </div>
            <div>
                <span>Rekomendasi Jual</span>
                <strong>Rp {{ number_format($formula->harga_jual_rekomendasi, 0, ',', '.') }}</strong>
            </div>
        </section>

        @if ($formula->catatan)
            <section class="panel">
                <strong>Catatan</strong>
                <p>{{ $formula->catatan }}</p>
            </section>
        @endif

        <section class="panel table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Bahan</th>
                        <th class="number">Qty</th>
                        <th>Satuan</th>
                        <th class="number">Harga Beli</th>
                        <th class="number">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($formula->items as $item)
                        <tr>
                            <td>{{ $item->kode_barang }}</td>
                            <td>{{ $item->nama_barang }}</td>
                            <td class="number">{{ rtrim(rtrim(number_format($item->qty, 3, ',', '.'), '0'), ',') }}</td>
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
