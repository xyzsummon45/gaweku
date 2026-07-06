<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kas</title>
    @include('barang.styles')
</head>
<body>
    @include('layouts.navbar')

    <main class="page">
        <header class="page-header">
            <div>
                <p>Keuangan Toko</p>
                <h1>Data Kas</h1>
            </div>
        </header>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <section class="panel summary-grid">
            @foreach ($kasAccounts as $kas)
                <div>
                    <span>{{ $kas->nama }}</span>
                    <strong>Rp {{ number_format($kas->saldo, 0, ',', '.') }}</strong>
                </div>
            @endforeach
        </section>

        <section class="panel">
            <h2>Tambah Mutasi Kas</h2>
            <form class="purchase-filter-form" method="POST" action="{{ route('kas.store') }}">
                @csrf

                <label>
                    <span>Jenis</span>
                    <select name="jenis" id="jenis" required>
                        <option value="pemasukan" @selected(old('jenis') === 'pemasukan')>Pemasukan</option>
                        <option value="pengeluaran" @selected(old('jenis') === 'pengeluaran')>Pengeluaran</option>
                        <option value="mutasi" @selected(old('jenis') === 'mutasi')>Mutasi Antar Kas</option>
                    </select>
                </label>

                <label class="single-kas">
                    <span>Kas</span>
                    <select name="kas_account_id">
                        <option value="">Pilih kas</option>
                        @foreach ($kasAccounts as $kas)
                            <option value="{{ $kas->id }}" @selected((string) old('kas_account_id') === (string) $kas->id)>{{ $kas->nama }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="transfer-kas">
                    <span>Dari Kas</span>
                    <select name="kas_asal_id">
                        <option value="">Pilih kas asal</option>
                        @foreach ($kasAccounts as $kas)
                            <option value="{{ $kas->id }}" @selected((string) old('kas_asal_id') === (string) $kas->id)>{{ $kas->nama }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="transfer-kas">
                    <span>Ke Kas</span>
                    <select name="kas_tujuan_id">
                        <option value="">Pilih kas tujuan</option>
                        @foreach ($kasAccounts as $kas)
                            <option value="{{ $kas->id }}" @selected((string) old('kas_tujuan_id') === (string) $kas->id)>{{ $kas->nama }}</option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span>Tanggal</span>
                    <input type="datetime-local" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d\TH:i')) }}" required>
                </label>

                <label>
                    <span>Jumlah</span>
                    <input type="number" name="jumlah" min="0.01" step="0.01" value="{{ old('jumlah') }}" required>
                </label>

                <label>
                    <span>Keterangan</span>
                    <input type="text" name="keterangan" value="{{ old('keterangan') }}" placeholder="Opsional">
                </label>

                <button type="submit">Simpan</button>
            </form>
        </section>

        <section class="panel table-wrap">
            <h2>Riwayat Mutasi</h2>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kas</th>
                        <th>Jenis</th>
                        <th>Referensi</th>
                        <th>Keterangan</th>
                        <th class="number">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mutations as $mutation)
                        <tr>
                            <td>{{ $mutation->tanggal->format('d/m/Y H:i') }}</td>
                            <td>{{ $mutation->kasAccount->nama }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $mutation->jenis)) }}</td>
                            <td>
                                @if ($mutation->pembelian)
                                    {{ $mutation->pembelian->kode_pembelian }}
                                @elseif ($mutation->transaksi)
                                    {{ $mutation->transaksi->kode_transaksi }}
                                @elseif ($mutation->relatedKasAccount)
                                    {{ $mutation->relatedKasAccount->nama }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $mutation->keterangan ?: '-' }}</td>
                            <td class="number">Rp {{ number_format($mutation->jumlah, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="empty" colspan="6">Belum ada mutasi kas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($mutations->hasPages())
                <div class="pagination">
                    <span>
                        Menampilkan {{ $mutations->firstItem() }}-{{ $mutations->lastItem() }}
                        dari {{ $mutations->total() }} mutasi
                    </span>
                    <div class="pagination-links">
                        @if ($mutations->onFirstPage())
                            <span class="secondary-button">Sebelumnya</span>
                        @else
                            <a class="secondary-button" href="{{ $mutations->previousPageUrl() }}">Sebelumnya</a>
                        @endif

                        @if ($mutations->hasMorePages())
                            <a class="secondary-button" href="{{ $mutations->nextPageUrl() }}">Berikutnya</a>
                        @else
                            <span class="secondary-button">Berikutnya</span>
                        @endif
                    </div>
                </div>
            @endif
        </section>
    </main>

    <script>
        const jenis = document.getElementById('jenis');
        const singleKas = document.querySelectorAll('.single-kas');
        const transferKas = document.querySelectorAll('.transfer-kas');

        function syncKasFields() {
            const isTransfer = jenis.value === 'mutasi';
            singleKas.forEach((field) => field.style.display = isTransfer ? 'none' : 'block');
            transferKas.forEach((field) => field.style.display = isTransfer ? 'block' : 'none');
        }

        jenis.addEventListener('change', syncKasFields);
        syncKasFields();
    </script>
</body>
</html>
