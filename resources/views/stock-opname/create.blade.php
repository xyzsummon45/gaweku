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
                    <div class="item-search">
                        <input id="barang-search" type="text" placeholder="Ketik minimal 2 huruf, contoh: semen" autocomplete="off">
                        <input id="barang-id" type="hidden" name="barang_id" value="{{ old('barang_id') }}">
                        <div id="suggestions" class="suggestions" hidden></div>
                    </div>
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
        const searchInput = document.getElementById('barang-search');
        const barangIdInput = document.getElementById('barang-id');
        const suggestions = document.getElementById('suggestions');
        const stokSistem = document.getElementById('stok-sistem');
        const autocompleteUrl = @json(route('stock-opname.autocomplete-barang'));
        let selectedBarang = null;
        let searchTimer = null;

        searchInput.addEventListener('input', () => {
            selectedBarang = null;
            barangIdInput.value = '';
            stokSistem.value = 'Pilih barang';
            clearTimeout(searchTimer);

            const keyword = searchInput.value.trim();

            if (keyword.length < 2) {
                suggestions.hidden = true;
                suggestions.innerHTML = '';
                return;
            }

            searchTimer = setTimeout(async () => {
                const response = await fetch(`${autocompleteUrl}?q=${encodeURIComponent(keyword)}`);
                const items = await response.json();
                renderSuggestions(items);
            }, 250);
        });

        function renderSuggestions(items) {
            suggestions.innerHTML = '';

            if (items.length === 0) {
                suggestions.hidden = true;
                return;
            }

            items.forEach((item) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'suggestion-item';
                button.innerHTML = `
                    <strong>${escapeHtml(item.nama_barang)}</strong>
                    <span>${escapeHtml(item.kode_barang)} | ${escapeHtml(item.jenis_barang)} | Stok ${formatQty(item.stok)} ${escapeHtml(item.satuan)}</span>
                `;
                button.addEventListener('click', () => {
                    selectedBarang = item;
                    barangIdInput.value = item.id;
                    searchInput.value = `${item.kode_barang} - ${item.nama_barang}`;
                    stokSistem.value = `${formatQty(item.stok)} ${item.satuan}`;
                    suggestions.hidden = true;
                });
                suggestions.appendChild(button);
            });

            suggestions.hidden = false;
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
