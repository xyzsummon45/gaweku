<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produksi Baru</title>
    @include('barang.styles')
</head>
<body>
    @include('layouts.navbar')

    <main class="page">
        <header class="page-header">
            <div>
                <p>Proses Manufaktur</p>
                <h1>Produksi Baru</h1>
            </div>
        </header>

        @if ($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        @if ($formulas->isEmpty())
            <div class="alert alert-info">Belum ada formula aktif. Buat formula dulu sebelum produksi.</div>
        @endif

        @if ($barangHasils->isEmpty())
            <div class="alert alert-info">Belum ada barang jadi. Buat barang dengan jenis Barang Jadi dulu sebelum produksi.</div>
        @endif

        <form method="POST" action="{{ route('produksi.store') }}" id="production-form">
            @csrf

            <section class="panel form-grid">
                <label>
                    <span>Formula</span>
                    <select name="formula_id" id="formula-select" required>
                        <option value="">Pilih formula</option>
                        @foreach ($formulas as $formula)
                            <option value="{{ $formula->id }}" @selected((string) old('formula_id') === (string) $formula->id)>
                                {{ $formula->nama_formula }} - {{ rtrim(rtrim(number_format($formula->qty_hasil, 3, ',', '.'), '0'), ',') }} {{ $formula->satuan_hasil }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span>Barang Hasil</span>
                    <select name="barang_hasil_id" required>
                        <option value="">Pilih barang hasil</option>
                        @foreach ($barangHasils as $barang)
                            <option value="{{ $barang->id }}" @selected((string) old('barang_hasil_id') === (string) $barang->id)>
                                {{ $barang->nama_barang }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span>Tanggal Produksi</span>
                    <input type="datetime-local" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d\TH:i')) }}" required>
                </label>

                <label>
                    <span>Qty Produksi</span>
                    <input id="qty-produksi" type="text" inputmode="decimal" name="qty_produksi" value="{{ old('qty_produksi', 1) }}" required>
                </label>

                <label>
                    <span>Catatan</span>
                    <textarea name="catatan">{{ old('catatan') }}</textarea>
                </label>
            </section>

            <section class="panel table-wrap">
                <div class="toolbar">
                    <div>
                        <strong>Biaya Tambahan</strong>
                    </div>
                    <button type="button" id="add-biaya">Tambah Biaya</button>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Nama Biaya</th>
                            <th class="number">Nominal</th>
                            <th class="number">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="biaya-body">
                        @foreach (old('biaya', [['nama_biaya' => '', 'nominal' => '']]) as $index => $biaya)
                            <tr>
                                <td>
                                    <input type="text" name="biaya[{{ $index }}][nama_biaya]" value="{{ $biaya['nama_biaya'] ?? '' }}" placeholder="Contoh: Listrik">
                                </td>
                                <td>
                                    <input class="number-input biaya-nominal" type="text" inputmode="decimal" name="biaya[{{ $index }}][nominal]" value="{{ $biaya['nominal'] ?? '' }}" placeholder="0">
                                </td>
                                <td class="number">
                                    <button class="secondary-button remove-biaya" type="button">Hapus</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="number" colspan="2">Total Biaya Tambahan</th>
                            <th id="total-biaya-tambahan" class="number">Rp 0</th>
                        </tr>
                    </tfoot>
                </table>
            </section>

            <section class="panel table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Bahan</th>
                            <th class="number">Stok</th>
                            <th class="number">Kebutuhan</th>
                            <th class="number">Harga Beli</th>
                            <th class="number">Subtotal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="material-body">
                        <tr>
                            <td class="empty" colspan="7">Pilih formula untuk melihat kebutuhan bahan.</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="number">Total Biaya Produksi</th>
                            <th id="total-biaya" class="number">Rp 0</th>
                            <th></th>
                        </tr>
                        <tr>
                            <th colspan="5" id="hpp-label" class="number">HPP per 1 satuan</th>
                            <th id="hpp" class="number">Rp 0</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </section>

            <div class="actions">
                <button type="submit">Simpan Produksi</button>
                <a href="{{ route('produksi.index') }}">Batal</a>
            </div>
        </form>
    </main>

    <script>
        const formulaSelect = document.getElementById('formula-select');
        const qtyProduksiInput = document.getElementById('qty-produksi');
        const materialBody = document.getElementById('material-body');
        const biayaBody = document.getElementById('biaya-body');
        const addBiaya = document.getElementById('add-biaya');
        const totalBiayaTambahan = document.getElementById('total-biaya-tambahan');
        const totalBiaya = document.getElementById('total-biaya');
        const hpp = document.getElementById('hpp');
        const hppLabel = document.getElementById('hpp-label');
        const formulas = @json($formulaOptions);
        let biayaIndex = {{ count(old('biaya', [['nama_biaya' => '', 'nominal' => '']])) }};

        const rupiah = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        });

        formulaSelect.addEventListener('change', renderMaterials);
        qtyProduksiInput.addEventListener('input', renderMaterials);
        biayaBody.addEventListener('input', renderMaterials);
        biayaBody.addEventListener('click', handleBiayaClick);
        addBiaya.addEventListener('click', addBiayaRow);
        renderMaterials();

        function renderMaterials() {
            const formula = formulas.find((item) => String(item.id) === formulaSelect.value);
            const qtyProduksi = parseDecimal(qtyProduksiInput.value);

            materialBody.innerHTML = '';

            if (! formula) {
                materialBody.innerHTML = '<tr><td class="empty" colspan="7">Pilih formula untuk melihat kebutuhan bahan.</td></tr>';
                const totalTambahan = calculateBiayaTambahan();
                totalBiayaTambahan.textContent = rupiah.format(totalTambahan);
                totalBiaya.textContent = rupiah.format(totalTambahan);
                hpp.textContent = rupiah.format(0);
                hppLabel.textContent = 'HPP per 1 satuan';
                return;
            }

            const ratio = Number.isFinite(qtyProduksi) && qtyProduksi > 0 ? qtyProduksi / formula.qty_hasil : 0;
            let total = 0;

            formula.items.forEach((item) => {
                const kebutuhan = item.qty * ratio;
                const subtotal = kebutuhan * item.harga_beli;
                const cukup = item.stok >= kebutuhan;
                total += subtotal;

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${escapeHtml(item.kode_barang)}</td>
                    <td>${escapeHtml(item.nama_barang)}</td>
                    <td class="number">${formatQty(item.stok)} ${escapeHtml(item.satuan)}</td>
                    <td class="number">${formatQty(kebutuhan)} ${escapeHtml(item.satuan)}</td>
                    <td class="number">${rupiah.format(item.harga_beli)}</td>
                    <td class="number">${rupiah.format(subtotal)}</td>
                    <td>${cukup ? 'Cukup' : 'Kurang'}</td>
                `;
                materialBody.appendChild(row);
            });

            const totalTambahan = calculateBiayaTambahan();
            total += totalTambahan;
            const hppValue = Number.isFinite(qtyProduksi) && qtyProduksi > 0 ? total / qtyProduksi : 0;
            totalBiayaTambahan.textContent = rupiah.format(totalTambahan);
            totalBiaya.textContent = rupiah.format(total);
            hpp.textContent = rupiah.format(hppValue);
            hppLabel.textContent = `HPP per 1 ${formula.satuan_hasil}`;
        }

        function addBiayaRow() {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <input type="text" name="biaya[${biayaIndex}][nama_biaya]" placeholder="Contoh: Listrik">
                </td>
                <td>
                    <input class="number-input biaya-nominal" type="text" inputmode="decimal" name="biaya[${biayaIndex}][nominal]" placeholder="0">
                </td>
                <td class="number">
                    <button class="secondary-button remove-biaya" type="button">Hapus</button>
                </td>
            `;
            biayaBody.appendChild(row);
            biayaIndex++;
        }

        function handleBiayaClick(event) {
            if (! event.target.classList.contains('remove-biaya')) {
                return;
            }

            const rows = biayaBody.querySelectorAll('tr');

            if (rows.length === 1) {
                rows[0].querySelectorAll('input').forEach((input) => input.value = '');
            } else {
                event.target.closest('tr').remove();
            }

            renderMaterials();
        }

        function calculateBiayaTambahan() {
            return Array.from(document.querySelectorAll('.biaya-nominal'))
                .reduce((sum, input) => {
                    const nominal = parseMoney(input.value);
                    return sum + (Number.isFinite(nominal) && nominal > 0 ? nominal : 0);
                }, 0);
        }

        function parseDecimal(value) {
            return Number.parseFloat(String(value).replace(',', '.'));
        }

        function parseMoney(value) {
            const normalized = String(value).trim().replaceAll('.', '').replace(',', '.');
            return Number.parseFloat(normalized);
        }

        function formatQty(value) {
            return new Intl.NumberFormat('id-ID', {
                maximumFractionDigits: 3,
            }).format(value);
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
</body>
</html>
