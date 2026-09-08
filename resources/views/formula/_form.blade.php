@csrf

@if ($errors->any())
    <div class="alert alert-error">{{ $errors->first() }}</div>
@endif

@if ($barangJadis->isEmpty())
    <div class="alert alert-info">
        Belum ada produk jadi. Buat barang dulu dengan jenis Barang Jadi, misalnya Tembok 1 m2.
    </div>
@endif

<section class="panel form-grid">
    <label>
        <span>Produk Jadi yang Dibuat</span>
        <select name="barang_jadi_id" id="barang-jadi" required>
            <option value="">Pilih barang jadi</option>
            @foreach ($barangJadis as $barang)
                <option
                    value="{{ $barang->id }}"
                    data-satuan="{{ $barang->satuan }}"
                    @selected((string) old('barang_jadi_id', $formula->barang_jadi_id) === (string) $barang->id)
                >
                    {{ $barang->nama_barang }} ({{ $barang->satuan }})
                </option>
            @endforeach
            @if ($barangJadis->isEmpty())
                <option value="" disabled>Belum ada barang dengan jenis Barang Jadi</option>
            @endif
        </select>
        @error('barang_jadi_id')
            <small>{{ $message }}</small>
        @enderror
    </label>

    <label>
        <span>Nama Formula</span>
        <input type="text" name="nama_formula" value="{{ old('nama_formula', $formula->nama_formula) }}" placeholder="Contoh: Formula Tembok 1 m2" required>
        @error('nama_formula')
            <small>{{ $message }}</small>
        @enderror
    </label>

    <label>
        <span>Jumlah Hasil</span>
        <input id="qty-hasil" type="text" inputmode="decimal" name="qty_hasil" value="{{ old('qty_hasil', $formula->qty_hasil ?: 1) }}" placeholder="Isi angka saja, contoh: 1" required>
        <span class="hint">Kalau hasilnya 1 m2, isi 1 di sini. Satuan m2 diambil dari produk jadi.</span>
        @error('qty_hasil')
            <small>{{ $message }}</small>
        @enderror
    </label>

    <label>
        <span>Satuan Hasil</span>
        <input id="satuan-hasil" type="text" value="Pilih produk jadi dulu" disabled>
    </label>

    <label>
        <span>Margin Keuntungan (%)</span>
        <input id="margin-persen" type="text" inputmode="decimal" name="margin_persen" value="{{ old('margin_persen', $formula->margin_persen ?: 0) }}" required>
        @error('margin_persen')
            <small>{{ $message }}</small>
        @enderror
    </label>

    <label>
        <span>Status</span>
        <select name="aktif" required>
            <option value="1" @selected((string) old('aktif', $formula->exists ? (int) $formula->aktif : 1) === '1')>Aktif</option>
            <option value="0" @selected((string) old('aktif', $formula->exists ? (int) $formula->aktif : 1) === '0')>Nonaktif</option>
        </select>
    </label>

    <label>
        <span>Catatan</span>
        <textarea name="catatan">{{ old('catatan', $formula->catatan) }}</textarea>
    </label>
</section>

<section class="panel cashier-panel">
    <label>
        <span>Bahan Baku / Penolong / Dagang</span>
        <select id="bahan-select">
            <option value="">Pilih bahan</option>
            @foreach ($barangBahans as $barang)
                <option
                    value="{{ $barang->id }}"
                    data-kode="{{ $barang->kode_barang }}"
                    data-nama="{{ $barang->nama_barang }}"
                    data-satuan="{{ $barang->satuan }}"
                    data-harga-beli="{{ (float) $barang->harga_beli }}"
                >
                    {{ $barang->nama_barang }} - {{ $barang->satuan }} - Rp {{ number_format($barang->harga_beli, 0, ',', '.') }}
                </option>
            @endforeach
            @if ($barangBahans->isEmpty())
                <option value="" disabled>Belum ada bahan yang bisa dipilih</option>
            @endif
        </select>
    </label>

    <label>
        <span>Jumlah Bahan</span>
        <input id="qty-bahan" type="text" inputmode="decimal" value="1" placeholder="Isi angka saja, contoh: 10">
        <span class="hint">Jumlah bahan untuk menghasilkan qty hasil di atas.</span>
    </label>

    <button id="add-bahan" type="button">Tambah Bahan</button>
</section>

<section class="panel table-wrap">
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Bahan</th>
                <th class="number">Qty</th>
                <th>Satuan</th>
                <th class="number">Harga Beli</th>
                <th class="number">Subtotal</th>
                <th class="number">Aksi</th>
            </tr>
        </thead>
        <tbody id="formula-body">
            <tr id="empty-row">
                <td class="empty" colspan="7">Belum ada bahan formula.</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="number">Total Biaya Formula</th>
                <th id="total-biaya" class="number">Rp 0</th>
                <th></th>
            </tr>
            <tr>
                <th id="hpp-label" colspan="5" class="number">HPP per 1 Satuan</th>
                <th id="hpp" class="number">Rp 0</th>
                <th></th>
            </tr>
            <tr>
                <th colspan="5" class="number">Rekomendasi Harga Jual</th>
                <th id="harga-jual-rekomendasi" class="number">Rp 0</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</section>

<div id="hidden-items"></div>

<div class="actions">
    <button type="submit">{{ $submit }}</button>
    <a href="{{ route('formula.index') }}">Batal</a>
</div>

<script>
    const bahanSelect = document.getElementById('bahan-select');
    const barangJadiSelect = document.getElementById('barang-jadi');
    const qtyBahanInput = document.getElementById('qty-bahan');
    const qtyHasilInput = document.getElementById('qty-hasil');
    const satuanHasilInput = document.getElementById('satuan-hasil');
    const marginInput = document.getElementById('margin-persen');
    const addBahanButton = document.getElementById('add-bahan');
    const formulaBody = document.getElementById('formula-body');
    const hiddenItems = document.getElementById('hidden-items');
    const totalBiaya = document.getElementById('total-biaya');
    const hppLabel = document.getElementById('hpp-label');
    const hpp = document.getElementById('hpp');
    const hargaJualRekomendasi = document.getElementById('harga-jual-rekomendasi');
    const form = document.getElementById('formula-form');
    const emptyRow = document.getElementById('empty-row');

    @php
        $initialItems = old('barang_bahan_id') ? collect(old('barang_bahan_id'))->map(function ($id, $index) {
            return ['id' => (int) $id, 'qty' => old('qty')[$index] ?? 0];
        })->values() : $formula->items->map(fn ($item) => [
            'id' => $item->barang_bahan_id,
            'qty' => (float) $item->qty,
        ])->values();
    @endphp

    const bahanItems = new Map();
    const initialItems = @json($initialItems);

    const rupiah = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    });

    addBahanButton.addEventListener('click', addBahan);
    barangJadiSelect.addEventListener('change', syncSatuanHasil);
    qtyHasilInput.addEventListener('input', renderFormula);
    marginInput.addEventListener('input', renderFormula);

    form.addEventListener('submit', (event) => {
        if (bahanItems.size === 0) {
            event.preventDefault();
            alert('Pilih minimal satu bahan formula.');
        }
    });

    initialItems.forEach((item) => {
        const option = bahanSelect.querySelector(`option[value="${item.id}"]`);
        if (! option) {
            return;
        }

        bahanItems.set(Number.parseInt(option.value, 10), {
            id: Number.parseInt(option.value, 10),
            kode_barang: option.dataset.kode,
            nama_barang: option.dataset.nama,
            satuan: option.dataset.satuan,
            harga_beli: Number.parseFloat(option.dataset.hargaBeli),
            qty: parseDecimal(item.qty),
        });
    });

    syncSatuanHasil();
    renderFormula();

    function addBahan() {
        const option = bahanSelect.selectedOptions[0];
        const qty = parseDecimal(qtyBahanInput.value);

        if (! option || ! option.value) {
            alert('Pilih bahan dulu.');
            bahanSelect.focus();
            return;
        }

        if (! Number.isFinite(qty) || qty < 0.001) {
            alert('Qty bahan harus lebih dari 0.');
            qtyBahanInput.focus();
            return;
        }

        const id = Number.parseInt(option.value, 10);
        bahanItems.set(id, {
            id,
            kode_barang: option.dataset.kode,
            nama_barang: option.dataset.nama,
            satuan: option.dataset.satuan,
            harga_beli: Number.parseFloat(option.dataset.hargaBeli),
            qty,
        });

        bahanSelect.value = '';
        qtyBahanInput.value = 1;
        renderFormula();
    }

    function renderFormula() {
        formulaBody.innerHTML = '';
        hiddenItems.innerHTML = '';

        let total = 0;

        if (bahanItems.size === 0) {
            formulaBody.appendChild(emptyRow);
        }

        bahanItems.forEach((item) => {
            const subtotal = item.qty * item.harga_beli;
            total += subtotal;

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${escapeHtml(item.kode_barang)}</td>
                <td>${escapeHtml(item.nama_barang)}</td>
                <td class="number">${formatQty(item.qty)}</td>
                <td>${escapeHtml(item.satuan)}</td>
                <td class="number">${rupiah.format(item.harga_beli)}</td>
                <td class="number">${rupiah.format(subtotal)}</td>
                <td class="number"><button class="danger-button" type="button" data-remove="${item.id}">Hapus</button></td>
            `;
            formulaBody.appendChild(row);

            hiddenItems.insertAdjacentHTML('beforeend', `
                <input type="hidden" name="barang_bahan_id[]" value="${item.id}">
                <input type="hidden" name="qty[]" value="${formatQtyForInput(item.qty)}">
            `);
        });

        const qtyHasil = parseDecimal(qtyHasilInput.value);
        const margin = parseDecimal(marginInput.value);
        const satuanHasil = selectedSatuanHasilForLabel();
        const hppValue = Number.isFinite(qtyHasil) && qtyHasil > 0 ? total / qtyHasil : 0;
        const rekomendasi = hppValue + (hppValue * ((Number.isFinite(margin) ? margin : 0) / 100));

        totalBiaya.textContent = rupiah.format(total);
        hppLabel.textContent = `HPP per 1 ${satuanHasil}`;
        hpp.textContent = rupiah.format(hppValue);
        hargaJualRekomendasi.textContent = rupiah.format(rekomendasi);

        formulaBody.querySelectorAll('[data-remove]').forEach((button) => {
            button.addEventListener('click', () => {
                bahanItems.delete(Number.parseInt(button.dataset.remove, 10));
                renderFormula();
            });
        });
    }

    function parseDecimal(value) {
        return Number.parseFloat(String(value).replace(',', '.'));
    }

    function syncSatuanHasil() {
        satuanHasilInput.value = selectedSatuanHasilForInput();
        renderFormula();
    }

    function selectedSatuanHasilForInput() {
        const option = barangJadiSelect.selectedOptions[0];

        return option?.dataset.satuan || 'Pilih produk jadi dulu';
    }

    function selectedSatuanHasilForLabel() {
        const option = barangJadiSelect.selectedOptions[0];

        return option?.dataset.satuan || 'satuan';
    }

    function formatQty(value) {
        return new Intl.NumberFormat('id-ID', {
            maximumFractionDigits: 3,
        }).format(value);
    }

    function formatQtyForInput(value) {
        return Number.parseFloat(value).toFixed(3).replace(/\.?0+$/, '');
    }

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }
</script>
