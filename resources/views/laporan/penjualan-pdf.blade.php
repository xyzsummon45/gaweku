<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Laba Rugi Jual</title>
    <style>
        body {
            color: #111827;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        h1 {
            font-size: 20px;
            margin: 0 0 4px;
        }

        p {
            margin: 0 0 14px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border-bottom: 1px solid #d1d5db;
            padding: 7px 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f3f4f6;
            color: #374151;
            font-size: 10px;
            text-transform: uppercase;
        }

        .summary {
            margin: 16px 0;
        }

        .summary td {
            border: 1px solid #d1d5db;
            padding: 10px;
        }

        .summary span {
            color: #4b5563;
            display: block;
            font-size: 10px;
            margin-bottom: 4px;
        }

        .summary strong {
            font-size: 15px;
        }

        .number {
            text-align: right;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <h1>T.B Global Jaya</h1>
    <p>Laporan Laba Rugi Jual: {{ \Carbon\Carbon::parse($filters['tanggal_mulai'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($filters['tanggal_selesai'])->format('d/m/Y') }}</p>

    <table class="summary">
        <tr>
            <td>
                <span>Total Penjualan</span>
                <strong>Rp {{ number_format($summary['total_penjualan'], 0, ',', '.') }}</strong>
            </td>
            <td>
                <span>Total Modal</span>
                <strong>Rp {{ number_format($summary['total_modal'], 0, ',', '.') }}</strong>
            </td>
            <td>
                <span>Laba Kotor</span>
                <strong>Rp {{ number_format($summary['laba_kotor'], 0, ',', '.') }}</strong>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Transaksi</th>
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
                    <td>{{ $item->kode_barang }} - {{ $item->nama_barang }}</td>
                    <td class="number">{{ rtrim(rtrim(number_format($item->qty, 3, ',', '.'), '0'), ',') }}</td>
                    <td class="number">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                    <td class="number">Rp {{ number_format($item->harga_modal, 0, ',', '.') }}</td>
                    <td class="number">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    <td class="number">Rp {{ number_format($item->subtotal_modal, 0, ',', '.') }}</td>
                    <td class="number">Rp {{ number_format($item->laba_kotor, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">Belum ada penjualan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
