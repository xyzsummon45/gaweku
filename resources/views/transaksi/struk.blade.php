<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $transaksi->kode_transaksi }}</title>
    <style>
        @page {
            margin: 0;
            size: 76mm auto;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: #fff;
            color: #000;
            font-family: "Courier New", monospace;
            font-size: 11px;
            line-height: 1.25;
            margin: 0;
            padding: 0;
        }

        .receipt {
            padding: 8px 6px;
            width: 76mm;
        }

        .center {
            text-align: center;
        }

        .title {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 8px;
        }

        .muted {
            margin-top: 2px;
        }

        .item {
            margin-bottom: 6px;
        }

        .item-name {
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .total {
            font-size: 13px;
            font-weight: 700;
        }

        .no-print {
            padding: 10px;
        }

        .no-print button {
            background: #0f766e;
            border: 0;
            border-radius: 6px;
            color: #fff;
            cursor: pointer;
            font-weight: 700;
            padding: 8px 12px;
        }

        @media print {
            .no-print {
                display: none;
            }

            .receipt {
                padding: 0 4px;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button type="button" onclick="window.print()">Print Struk</button>
    </div>

    <main class="receipt">
        <div class="center">
            <div class="title">T.B GLOBAL JAYA</div>
            <div>Struk Penjualan</div>
        </div>

        <div class="line"></div>

        <div>Kode: {{ $transaksi->kode_transaksi }}</div>
        <div>Tgl : {{ $transaksi->tanggal->format('d/m/Y H:i') }}</div>

        <div class="line"></div>

        @foreach ($transaksi->items as $item)
            <div class="item">
                <div class="item-name">{{ $item->nama_barang }}</div>
                <div class="row muted">
                    <span>{{ rtrim(rtrim(number_format($item->qty, 3, ',', '.'), '0'), ',') }} x {{ number_format($item->harga_jual, 0, ',', '.') }}</span>
                    <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
            </div>
        @endforeach

        <div class="line"></div>

        <div class="row total">
            <span>TOTAL</span>
            <span>Rp {{ number_format($transaksi->total, 0, ',', '.') }}</span>
        </div>

        <div class="line"></div>

        <div class="center">
            <div>Terima kasih</div>
            <div>Barang yang sudah dibeli</div>
            <div>tidak dapat dikembalikan</div>
        </div>
    </main>

    <script>
        window.addEventListener('load', () => {
            setTimeout(() => window.print(), 300);
        });
    </script>
</body>
</html>
