@csrf

<div class="form-grid">
    <label>
        <span>Nama Supplier</span>
        <input type="text" name="nama_supplier" value="{{ old('nama_supplier', $supplier->nama_supplier) }}" required>
        @error('nama_supplier')
            <small>{{ $message }}</small>
        @enderror
    </label>

    <label>
        <span>No HP</span>
        <input type="text" name="no_hp" value="{{ old('no_hp', $supplier->no_hp) }}">
        @error('no_hp')
            <small>{{ $message }}</small>
        @enderror
    </label>

    <label>
        <span>Alamat</span>
        <textarea name="alamat">{{ old('alamat', $supplier->alamat) }}</textarea>
        @error('alamat')
            <small>{{ $message }}</small>
        @enderror
    </label>

    <label>
        <span>Catatan</span>
        <textarea name="catatan">{{ old('catatan', $supplier->catatan) }}</textarea>
        @error('catatan')
            <small>{{ $message }}</small>
        @enderror
    </label>
</div>

<div class="actions">
    <button type="submit">{{ $submit }}</button>
    <a href="{{ route('supplier.index') }}">Batal</a>
</div>
