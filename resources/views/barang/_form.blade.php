@csrf

<div class="form-grid">
    <label>
        <span>Kode Barang</span>
        <input type="text" name="kode_barang" value="{{ old('kode_barang', $barang->kode_barang) }}" required>
        @error('kode_barang')
            <small>{{ $message }}</small>
        @enderror
    </label>

    <label>
        <span>Nama Barang</span>
        <input type="text" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" required>
        @error('nama_barang')
            <small>{{ $message }}</small>
        @enderror
    </label>

    <label>
        <span>Jenis Barang</span>
        <select name="jenis_barang" id="jenis-barang" required>
            @foreach ([
                'bahan_baku' => 'Bahan Baku',
                'barang_jadi' => 'Barang Jadi',
                'barang_dagang' => 'Barang Dagang',
                'bahan_penolong' => 'Bahan Penolong',
            ] as $value => $label)
                <option value="{{ $value }}" @selected(old('jenis_barang', $barang->jenis_barang ?: 'barang_dagang') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('jenis_barang')
            <small>{{ $message }}</small>
        @enderror
    </label>

    <label>
        <span>Satuan</span>
        <input type="text" name="satuan" value="{{ old('satuan', $barang->satuan ?: 'pcs') }}" placeholder="pcs, kg, sak, m2" required>
        @error('satuan')
            <small>{{ $message }}</small>
        @enderror
    </label>

    <label>
        <span>Harga Beli</span>
        <input type="number" name="harga_beli" id="harga-beli" value="{{ old('harga_beli', $barang->harga_beli) }}" min="0" step="0.01" placeholder="0" required>
        @error('harga_beli')
            <small>{{ $message }}</small>
        @enderror
    </label>

    <label>
        <span>Harga Jual</span>
        <input type="number" name="harga_jual" id="harga-jual" value="{{ old('harga_jual', $barang->harga_jual) }}" min="0" step="0.01" placeholder="0" required>
        @error('harga_jual')
            <small>{{ $message }}</small>
        @enderror
    </label>

    <label>
        <span>Stok</span>
        <input type="number" name="stok" id="stok" value="{{ old('stok', $barang->stok) }}" min="0" step="0.001" placeholder="0" required>
        @error('stok')
            <small>{{ $message }}</small>
        @enderror
    </label>
</div>

<div class="actions">
    <button type="submit">{{ $submit }}</button>
    <a href="{{ route('barang.index') }}">Batal</a>
</div>

<script>
    const jenisBarang = document.getElementById('jenis-barang');
    const productionFields = [
        document.getElementById('harga-beli'),
        document.getElementById('harga-jual'),
        document.getElementById('stok'),
    ];

    jenisBarang.addEventListener('change', syncProductionFields);
    syncProductionFields();

    function syncProductionFields() {
        const isBarangJadi = jenisBarang.value === 'barang_jadi';

        productionFields.forEach((field) => {
            field.required = ! isBarangJadi;
        });
    }
</script>
