<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Laba Rugi Jual</title>
    @include('barang.styles')
</head>
<body>
    @include('layouts.navbar')

    <main class="page">
        <header class="page-header">
            <div>
                <p>Laporan</p>
                <h1>Laba Rugi Jual</h1>
            </div>
            <a
                class="primary-button"
                href="{{ route('laporan.penjualan.pdf', ['tanggal_mulai' => $filters['tanggal_mulai'], 'tanggal_selesai' => $filters['tanggal_selesai']]) }}"
            >
                Download PDF
            </a>
        </header>

        @if ($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <section class="panel">
            <form class="filter-form" method="GET" action="{{ route('laporan.penjualan') }}">
                <label>
                    <span>Dari Tanggal</span>
                    <input type="date" name="tanggal_mulai" value="{{ $filters['tanggal_mulai'] }}">
                </label>

                <label>
                    <span>Sampai Tanggal</span>
                    <input type="date" name="tanggal_selesai" value="{{ $filters['tanggal_selesai'] }}">
                </label>

                <button type="submit">Cari</button>
                <a class="secondary-button" href="{{ route('laporan.penjualan') }}">Reset</a>
            </form>
        </section>

        <section class="panel summary-grid">
            <div>
                <span>Total Penjualan</span>
                <strong>Rp {{ number_format($summary['total_penjualan'], 0, ',', '.') }}</strong>
            </div>
            <div>
                <span>Total Modal</span>
                <strong>Rp {{ number_format($summary['total_modal'], 0, ',', '.') }}</strong>
            </div>
            <div>
                <span>Laba Kotor</span>
                <strong>Rp {{ number_format($summary['laba_kotor'], 0, ',', '.') }}</strong>
            </div>
        </section>

        <section class="panel table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kode Transaksi</th>
                        <th>Kode Barang</th>
                        <th>Barang</th>
                        <th class="number">Qty</th>
                        <th class="number">Harga Jual</th>
                        <th class="number">Harga Modal</th>
                        <th class="number">Penjualan</th>
                        <th class="number">Modal</th>
                        <th class="number">Laba Kotor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td>{{ $item->transaksi->tanggal->format('d/m/Y H:i') }}</td>
                            <td>{{ $item->transaksi->kode_transaksi }}</td>
                            <td>{{ $item->kode_barang }}</td>
                            <td>{{ $item->nama_barang }}</td>
                            <td class="number">{{ rtrim(rtrim(number_format($item->qty, 3, ',', '.'), '0'), ',') }}</td>
                            <td class="number">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                            <td class="number">Rp {{ number_format($item->harga_modal, 0, ',', '.') }}</td>
                            <td class="number">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            <td class="number">Rp {{ number_format($item->subtotal_modal, 0, ',', '.') }}</td>
                            <td class="number">Rp {{ number_format($item->laba_kotor, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="empty" colspan="10">Belum ada penjualan pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
