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
        <span>Harga Beli</span>
        <input type="number" name="harga_beli" value="{{ old('harga_beli', $barang->harga_beli) }}" min="0" step="0.01" required>
        @error('harga_beli')
            <small>{{ $message }}</small>
        @enderror
    </label>

    <label>
        <span>Harga Jual</span>
        <input type="number" name="harga_jual" value="{{ old('harga_jual', $barang->harga_jual) }}" min="0" step="0.01" required>
        @error('harga_jual')
            <small>{{ $message }}</small>
        @enderror
    </label>

    <label>
        <span>Stok</span>
        <input type="number" name="stok" value="{{ old('stok', $barang->stok) }}" min="0" step="1" required>
        @error('stok')
            <small>{{ $message }}</small>
        @enderror
    </label>
</div>

<div class="actions">
    <button type="submit">{{ $submit }}</button>
    <a href="{{ route('barang.index') }}">Batal</a>
</div>
