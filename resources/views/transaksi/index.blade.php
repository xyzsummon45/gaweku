<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Transaksi</title>
    @include('transaksi.styles')
</head>
<body>
    @include('layouts.navbar')

    <main class="page">
        <header class="page-header">
            <div>
                <p>Kasir</p>
                <h1>Data Transaksi</h1>
            </div>
            <a class="primary-button" href="{{ route('transaksi.create') }}">Transaksi Baru</a>
        </header>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <section class="panel">
            <form class="filter-form" method="GET" action="{{ route('transaksi.index') }}">
                <label>
                    <span>Dari Tanggal</span>
                    <input type="date" name="tanggal_mulai" value="{{ $filters['tanggal_mulai'] ?? '' }}">
                </label>

                <label>
                    <span>Sampai Tanggal</span>
                    <input type="date" name="tanggal_selesai" value="{{ $filters['tanggal_selesai'] ?? '' }}">
                </label>

                <button type="submit">Cari</button>
                <a class="secondary-button" href="{{ route('transaksi.index') }}">Reset</a>
            </form>
        </section>

        <section class="panel table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kode Transaksi</th>
                        <th>Tanggal</th>
                        <th class="number">Item</th>
                        <th class="number">Total</th>
                        <th class="number">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transaksis as $transaksi)
                        <tr>
                            <td>{{ $transaksi->kode_transaksi }}</td>
                            <td>{{ $transaksi->tanggal->format('d/m/Y H:i') }}</td>
                            <td class="number">{{ $transaksi->items_count }}</td>
                            <td class="number">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</td>
                            <td class="number">
                                <a class="secondary-button" href="{{ route('transaksi.show', $transaksi) }}">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="empty" colspan="5">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($transaksis->hasPages())
                <div class="pagination">
                    <span>
                        Menampilkan {{ $transaksis->firstItem() }}-{{ $transaksis->lastItem() }}
                        dari {{ $transaksis->total() }} transaksi
                    </span>
                    <div class="pagination-links">
                        @if ($transaksis->onFirstPage())
                            <span class="secondary-button">Sebelumnya</span>
                        @else
                            <a class="secondary-button" href="{{ $transaksis->previousPageUrl() }}">Sebelumnya</a>
                        @endif

                        @if ($transaksis->hasMorePages())
                            <a class="secondary-button" href="{{ $transaksis->nextPageUrl() }}">Berikutnya</a>
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
