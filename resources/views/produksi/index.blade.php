<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Produksi</title>
    @include('barang.styles')
</head>
<body>
    @include('layouts.navbar')

    <main class="page">
        <header class="page-header">
            <div>
                <p>Proses Manufaktur</p>
                <h1>Data Produksi</h1>
            </div>
            <a class="primary-button" href="{{ route('produksi.create') }}">Produksi Baru</a>
        </header>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <section class="panel">
            <form class="purchase-filter-form" method="GET" action="{{ route('produksi.index') }}">
                <label>
                    <span>Cari</span>
                    <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari kode, formula, atau barang hasil">
                </label>

                <label>
                    <span>Dari Tanggal</span>
                    <input type="date" name="tanggal_mulai" value="{{ $filters['tanggal_mulai'] ?? '' }}">
                </label>

                <label>
                    <span>Sampai Tanggal</span>
                    <input type="date" name="tanggal_selesai" value="{{ $filters['tanggal_selesai'] ?? '' }}">
                </label>

                <button type="submit">Cari</button>
                <a class="secondary-button" href="{{ route('produksi.index') }}">Reset</a>
            </form>
        </section>

        <section class="panel table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Tanggal</th>
                        <th>Formula</th>
                        <th>Barang Hasil</th>
                        <th class="number">Qty Produksi</th>
                        <th class="number">Bahan</th>
                        <th class="number">Total Biaya</th>
                        <th class="number">HPP</th>
                        <th class="number">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($produksis as $produksi)
                        <tr>
                            <td>{{ $produksi->kode_produksi }}</td>
                            <td>{{ $produksi->tanggal->format('d/m/Y H:i') }}</td>
                            <td>{{ $produksi->nama_formula }}</td>
                            <td>{{ $produksi->barangHasil->nama_barang }}</td>
                            <td class="number">{{ rtrim(rtrim(number_format($produksi->qty_produksi, 3, ',', '.'), '0'), ',') }} {{ $produksi->satuan_hasil }}</td>
                            <td class="number">{{ $produksi->items_count }}</td>
                            <td class="number">Rp {{ number_format($produksi->total_biaya, 0, ',', '.') }}</td>
                            <td class="number">Rp {{ number_format($produksi->hpp, 0, ',', '.') }}</td>
                            <td class="number">
                                <a class="secondary-button" href="{{ route('produksi.show', $produksi) }}">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="empty" colspan="9">Belum ada data produksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($produksis->hasPages())
                <div class="pagination">
                    <span>
                        Menampilkan {{ $produksis->firstItem() }}-{{ $produksis->lastItem() }}
                        dari {{ $produksis->total() }} produksi
                    </span>
                    <div class="pagination-links">
                        @if ($produksis->onFirstPage())
                            <span class="secondary-button">Sebelumnya</span>
                        @else
                            <a class="secondary-button" href="{{ $produksis->previousPageUrl() }}">Sebelumnya</a>
                        @endif

                        @if ($produksis->hasMorePages())
                            <a class="secondary-button" href="{{ $produksis->nextPageUrl() }}">Berikutnya</a>
                        @else
                            <span class="secondary-button">Berikutnya</span>
                        @endif
                    </div>
                </div>
            @endif
        </section>
    </main>
</body>
</html>
