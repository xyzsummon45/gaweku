<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pembelian</title>
    @include('barang.styles')
</head>
<body>
    @include('layouts.navbar')

    <main class="page">
        <header class="page-header">
            <div>
                <p>Invoice Supplier</p>
                <h1>Data Pembelian</h1>
            </div>
            <a class="primary-button" href="{{ route('pembelian.create') }}">Pembelian Baru</a>
        </header>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <section class="panel">
            <form class="purchase-filter-form" method="GET" action="{{ route('pembelian.index') }}">
                <label>
                    <span>Cari</span>
                    <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari kode, invoice, atau supplier">
                </label>

                <label>
                    <span>Dari Tanggal</span>
                    <input type="date" name="tanggal_mulai" value="{{ $filters['tanggal_mulai'] ?? '' }}">
                </label>

                <label>
                    <span>Sampai Tanggal</span>
                    <input type="date" name="tanggal_selesai" value="{{ $filters['tanggal_selesai'] ?? '' }}">
                </label>

                <label>
                    <span>Status</span>
                    <select name="status_pembayaran">
                        <option value="">Semua</option>
                        <option value="belum_lunas" @selected(($filters['status_pembayaran'] ?? '') === 'belum_lunas')>Belum Lunas</option>
                        <option value="sebagian" @selected(($filters['status_pembayaran'] ?? '') === 'sebagian')>Sebagian</option>
                        <option value="lunas" @selected(($filters['status_pembayaran'] ?? '') === 'lunas')>Lunas</option>
                    </select>
                </label>

                <button type="submit">Cari</button>
                <a class="secondary-button" href="{{ route('pembelian.index') }}">Reset</a>
            </form>
        </section>

        <section class="panel table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Invoice</th>
                        <th>Supplier</th>
                        <th>Tanggal</th>
                        <th>Jatuh Tempo</th>
                        <th>Status</th>
                        <th class="number">Item</th>
                        <th class="number">Total</th>
                        <th class="number">Dibayar</th>
                        <th class="number">Sisa</th>
                        <th class="number">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pembelians as $pembelian)
                        <tr>
                            <td>{{ $pembelian->kode_pembelian }}</td>
                            <td>{{ $pembelian->nomor_invoice }}</td>
                            <td>{{ $pembelian->supplier->nama_supplier }}</td>
                            <td>{{ $pembelian->tanggal->format('d/m/Y') }}</td>
                            <td>{{ $pembelian->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                            <td>{{ str_replace('_', ' ', ucfirst($pembelian->status_pembayaran)) }}</td>
                            <td class="number">{{ $pembelian->items_count }}</td>
                            <td class="number">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</td>
                            <td class="number">Rp {{ number_format($pembelian->jumlah_dibayar, 0, ',', '.') }}</td>
                            <td class="number">Rp {{ number_format($pembelian->sisaHutang(), 0, ',', '.') }}</td>
                            <td class="number">
                                <a class="secondary-button" href="{{ route('pembelian.show', $pembelian) }}">Detail</a>
                                @if ($pembelian->status_pembayaran !== 'lunas')
                                    <a class="primary-button" href="{{ route('pembelian.show', $pembelian) }}#bayar-hutang">Bayar</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="empty" colspan="11">Belum ada data pembelian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($pembelians->hasPages())
                <div class="pagination">
                    <span>
                        Menampilkan {{ $pembelians->firstItem() }}-{{ $pembelians->lastItem() }}
                        dari {{ $pembelians->total() }} pembelian
                    </span>
                    <div class="pagination-links">
                        @if ($pembelians->onFirstPage())
                            <span class="secondary-button">Sebelumnya</span>
                        @else
                            <a class="secondary-button" href="{{ $pembelians->previousPageUrl() }}">Sebelumnya</a>
                        @endif

                        @if ($pembelians->hasMorePages())
                            <a class="secondary-button" href="{{ $pembelians->nextPageUrl() }}">Berikutnya</a>
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
