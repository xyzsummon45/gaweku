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
        </section>
    </main>
</body>
</html>
