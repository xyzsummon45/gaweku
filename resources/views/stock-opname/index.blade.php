<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Opname</title>
    @include('barang.styles')
</head>
<body>
    @include('layouts.navbar')

    <main class="page">
        <header class="page-header">
            <div>
                <p>Inventory</p>
                <h1>Stock Opname</h1>
            </div>
            <a class="primary-button" href="{{ route('stock-opname.create') }}">Opname Baru</a>
        </header>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <section class="panel">
            <form class="filter-form" method="GET" action="{{ route('stock-opname.index') }}">
                <label>
                    <span>Cari</span>
                    <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari kode, nama barang, atau catatan">
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
                <a class="secondary-button" href="{{ route('stock-opname.index') }}">Reset</a>
            </form>
        </section>

        <section class="panel table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kode</th>
                        <th>Barang</th>
                        <th class="number">Stok Sebelum</th>
                        <th class="number">Stok Fisik</th>
                        <th class="number">Selisih</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($opnames as $opname)
                        @php
                            $selisih = (float) $opname->stok_sesudah - (float) $opname->stok_sebelum;
                            $satuan = $opname->barang?->satuan ?? '';
                        @endphp
                        <tr>
                            <td>{{ $opname->tanggal->format('d/m/Y H:i') }}</td>
                            <td>{{ $opname->barang?->kode_barang ?? '-' }}</td>
                            <td>{{ $opname->barang?->nama_barang ?? '-' }}</td>
                            <td class="number">{{ rtrim(rtrim(number_format($opname->stok_sebelum, 3, ',', '.'), '0'), ',') }} {{ $satuan }}</td>
                            <td class="number">{{ rtrim(rtrim(number_format($opname->stok_sesudah, 3, ',', '.'), '0'), ',') }} {{ $satuan }}</td>
                            <td class="number">{{ $selisih > 0 ? '+' : '' }}{{ rtrim(rtrim(number_format($selisih, 3, ',', '.'), '0'), ',') }} {{ $satuan }}</td>
                            <td>{{ $opname->catatan ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="empty" colspan="7">Belum ada stock opname.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($opnames->hasPages())
                <div class="pagination">
                    <span>
                        Menampilkan {{ $opnames->firstItem() }}-{{ $opnames->lastItem() }}
                        dari {{ $opnames->total() }} opname
                    </span>
                    <div class="pagination-links">
                        @if ($opnames->onFirstPage())
                            <span class="secondary-button">Sebelumnya</span>
                        @else
                            <a class="secondary-button" href="{{ $opnames->previousPageUrl() }}">Sebelumnya</a>
                        @endif

                        @if ($opnames->hasMorePages())
                            <a class="secondary-button" href="{{ $opnames->nextPageUrl() }}">Berikutnya</a>
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
