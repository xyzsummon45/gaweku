<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pembelian</title>
    @include('barang.styles')
</head>
<body>
    @include('layouts.navbar')

    <main class="page">
        <header class="page-header">
            <div>
                <p>{{ $pembelian->kode_pembelian }}</p>
                <h1>Detail Pembelian</h1>
            </div>
            <a class="primary-button" href="{{ route('pembelian.create') }}">Pembelian Baru</a>
        </header>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <section class="panel summary-grid">
            <div>
                <span>Supplier</span>
                <strong>{{ $pembelian->supplier->nama_supplier }}</strong>
            </div>
            <div>
                <span>Nomor Invoice</span>
                <strong>{{ $pembelian->nomor_invoice }}</strong>
            </div>
            <div>
                <span>Jatuh Tempo</span>
                <strong>{{ $pembelian->tanggal_jatuh_tempo->format('d/m/Y') }}</strong>
            </div>
            <div>
                <span>Status</span>
                <strong>{{ str_replace('_', ' ', ucfirst($pembelian->status_pembayaran)) }}</strong>
            </div>
            <div>
                <span>Total Hutang</span>
                <strong>Rp {{ number_format($pembelian->total, 0, ',', '.') }}</strong>
            </div>
            <div>
                <span>Sudah Dibayar</span>
                <strong>Rp {{ number_format($pembelian->jumlah_dibayar, 0, ',', '.') }}</strong>
            </div>
            <div>
                <span>Sisa Hutang</span>
                <strong>Rp {{ number_format($pembelian->sisaHutang(), 0, ',', '.') }}</strong>
            </div>
        </section>

        @if ($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        @if ($pembelian->status_pembayaran !== 'lunas')
            <section id="bayar-hutang" class="panel">
                <h2>Bayar Hutang Supplier</h2>
                <form class="purchase-filter-form" method="POST" action="{{ route('pembelian.bayar', $pembelian) }}">
                    @csrf

                    <label>
                        <span>Bayar Dari Kas</span>
                        <select name="kas_account_id" required>
                            <option value="">Pilih kas</option>
                            @foreach ($kasAccounts as $kas)
                                <option value="{{ $kas->id }}" @selected((string) old('kas_account_id', $kasAccounts->firstWhere('kode', 'kas_bank')?->id) === (string) $kas->id)>
                                    {{ $kas->nama }} - Rp {{ number_format($kas->saldo, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <label>
                        <span>Tanggal Bayar</span>
                        <input type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" required>
                    </label>

                    <label>
                        <span>Jumlah Bayar</span>
                        <input type="number" name="jumlah" min="0.01" step="0.01" value="{{ old('jumlah', $pembelian->sisaHutang()) }}" required>
                    </label>

                    <label>
                        <span>Catatan</span>
                        <input type="text" name="catatan" value="{{ old('catatan') }}" placeholder="Opsional">
                    </label>

                    <button type="submit">Simpan Pembayaran</button>
                </form>
            </section>
        @endif

        @if ($pembelian->kasMutations->isNotEmpty())
            <section class="panel table-wrap">
                <h2>Riwayat Pembayaran</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kas</th>
                            <th>Keterangan</th>
                            <th class="number">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pembelian->kasMutations as $mutation)
                            <tr>
                                <td>{{ $mutation->tanggal->format('d/m/Y H:i') }}</td>
                                <td>{{ $mutation->kasAccount->nama }}</td>
                                <td>{{ $mutation->keterangan ?: '-' }}</td>
                                <td class="number">Rp {{ number_format($mutation->jumlah, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
        @endif

        @if ($pembelian->catatan)
            <section class="panel">
                <strong>Catatan</strong>
                <p>{{ $pembelian->catatan }}</p>
            </section>
        @endif

        <section class="panel table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th class="number">Qty</th>
                        <th class="number">Harga Beli</th>
                        <th class="number">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pembelian->items as $item)
                        <tr>
                            <td>{{ $item->kode_barang }}</td>
                            <td>{{ $item->nama_barang }}</td>
                            <td class="number">{{ rtrim(rtrim(number_format($item->qty, 3, ',', '.'), '0'), ',') }}</td>
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
