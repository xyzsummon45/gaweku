<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Barang</title>
    @include('barang.styles')
</head>
<body>
    @include('layouts.navbar')

    <main class="page">
        <header class="page-header">
            <div>
                <p>Master Barang</p>
                <h1>Data Barang</h1>
            </div>
            <a class="primary-button" href="{{ route('barang.create') }}">Tambah Barang</a>
        </header>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <section class="panel">
            <form class="search-form" method="GET" action="{{ route('barang.index') }}">
                <label>
                    <span>Cari Barang</span>
                    <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari kode atau nama barang, contoh: semen">
                </label>

                <button type="submit">Cari</button>
                <a class="secondary-button" href="{{ route('barang.index') }}">Reset</a>
            </form>
        </section>

        <section class="panel toolbar">
            <div>
                <strong>Import Excel</strong>
                <p>Gunakan header: kode_barang, nama_barang, harga_beli, harga_jual, stok.</p>
            </div>
            <form class="import-form" method="POST" action="{{ route('barang.import') }}" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" accept=".xls,.xlsx" required>
                <button type="submit">Import</button>
            </form>
        </section>

        <section class="panel table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th class="number">Harga Beli</th>
                        <th class="number">Harga Jual</th>
                        <th class="number">Stok</th>
                        <th class="number">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($barangs as $barang)
                        <tr>
                            <td>{{ $barang->kode_barang }}</td>
                            <td>{{ $barang->nama_barang }}</td>
                            <td class="number">Rp {{ number_format($barang->harga_beli, 0, ',', '.') }}</td>
                            <td class="number">Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</td>
                            <td class="number">{{ rtrim(rtrim(number_format($barang->stok, 3, ',', '.'), '0'), ',') }}</td>
                            <td>
                                <div class="row-actions">
                                    <a class="secondary-button" href="{{ route('barang.edit', $barang) }}">Edit</a>
                                    <form method="POST" action="{{ route('barang.destroy', $barang) }}" onsubmit="return confirm('Hapus barang ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="danger-button" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="empty" colspan="6">Belum ada data barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($barangs->hasPages())
                <div class="pagination">
                    <span>
                        Menampilkan {{ $barangs->firstItem() }}-{{ $barangs->lastItem() }}
                        dari {{ $barangs->total() }} barang
                    </span>
                    <div class="pagination-links">
                        @if ($barangs->onFirstPage())
                            <span class="secondary-button">Sebelumnya</span>
                        @else
                            <a class="secondary-button" href="{{ $barangs->previousPageUrl() }}">Sebelumnya</a>
                        @endif

                        @if ($barangs->hasMorePages())
                            <a class="secondary-button" href="{{ $barangs->nextPageUrl() }}">Berikutnya</a>
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
