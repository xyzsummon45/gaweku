<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opname Baru</title>
    @include('barang.styles')
</head>
<body>
    @include('layouts.navbar')

    <main class="page">
        <header class="page-header">
            <div>
                <p>Inventory</p>
                <h1>Opname Baru</h1>
            </div>
        </header>

        @if ($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('stock-opname.store') }}">
            @csrf

            <section class="panel form-grid">
                <label>
                    <span>Barang</span>
                    <select name="barang_id" id="barang-select" required>
                        <option value="">Pilih barang</option>
                        @foreach ($barangs as $barang)
                            <option
                                value="{{ $barang->id }}"
                                data-stok="{{ (float) $barang->stok }}"
                                data-satuan="{{ $barang->satuan }}"
                                @selected((string) old('barang_id') === (string) $barang->id)
                            >
                                {{ $barang->kode_barang }} - {{ $barang->nama_barang }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span>Tanggal Opname</span>
                    <input type="datetime-local" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d\TH:i')) }}" required>
                </label>

                <label>
                    <span>Stok Sistem</span>
                    <input type="text" id="stok-sistem" value="Pilih barang" readonly>
                </label>

                <label>
                    <span>Stok Fisik</span>
                    <input type="text" inputmode="decimal" name="stok_fisik" value="{{ old('stok_fisik') }}" placeholder="Contoh: 4,5" required>
                </label>

                <label>
                    <span>Catatan</span>
                    <textarea name="catatan" placeholder="Contoh: opname bulanan, barang rusak, selisih gudang">{{ old('catatan') }}</textarea>
                </label>
            </section>

            <div class="actions">
                <button type="submit">Simpan Opname</button>
                <a href="{{ route('stock-opname.index') }}">Batal</a>
            </div>
        </form>
    </main>

    <script>
        const barangSelect = document.getElementById('barang-select');
        const stokSistem = document.getElementById('stok-sistem');

        barangSelect.addEventListener('change', syncStock);
        syncStock();

        function syncStock() {
            const option = barangSelect.selectedOptions[0];

            if (! option || ! option.value) {
                stokSistem.value = 'Pilih barang';
                return;
            }

            const stock = Number.parseFloat(option.dataset.stok || '0');
            const satuan = option.dataset.satuan || '';
            stokSistem.value = `${new Intl.NumberFormat('id-ID', { maximumFractionDigits: 3 }).format(stock)} ${satuan}`;
        }
    </script>
</body>
</html>
