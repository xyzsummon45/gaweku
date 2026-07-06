<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembelian Baru</title>
    @include('barang.styles')
</head>
<body>
    @include('layouts.navbar')

    <main class="page">
        <header class="page-header">
            <div>
                <p>Invoice Supplier</p>
                <h1>Pembelian Baru</h1>
            </div>
        </header>

        @if ($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('pembelian.store') }}" id="purchase-form">
            @csrf

            <section class="panel form-grid">
                <label>
                    <span>Supplier</span>
                    <select name="supplier_id" required>
                        <option value="">Pilih supplier</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected((string) old('supplier_id') === (string) $supplier->id)>
                                {{ $supplier->nama_supplier }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span>Nomor Invoice</span>
                    <input type="text" name="nomor_invoice" value="{{ old('nomor_invoice') }}" required>
                </label>

                <label>
                    <span>Tanggal Invoice</span>
                    <input type="date" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                </label>

                <label>
                    <span>Tanggal Jatuh Tempo</span>
                    <input type="date" name="tanggal_jatuh_tempo" value="{{ old('tanggal_jatuh_tempo', now()->format('Y-m-d')) }}" required>
                </label>

                <label>
                    <span>Catatan</span>
                    <textarea name="catatan">{{ old('catatan') }}</textarea>
                </label>
            </section>

            <section class="panel cashier-panel">
                <div class="item-search">
                    <label>
                        <span>Barang</span>
                        <input id="barang-search" type="text" placeholder="Ketik minimal 2 huruf, contoh: semen" autocomplete="off">
                    </label>
                    <div id="suggestions" class="suggestions" hidden></div>
                </div>

                <label>
                    <span>Qty</span>
                    <input id="qty-input" type="text" inputmode="decimal" value="1">
                </label>

                <label>
                    <span>Harga Beli</span>
                    <input id="price-input" type="text" inputmode="decimal" value="0">
                </label>

                <button id="add-item" type="button">Tambah</button>
            </section>

            <section class="panel table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th class="number">Qty</th>
                            <th class="number">Harga Beli</th>
                            <th class="number">Subtotal</th>
                            <th class="number">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="cart-body">
                        <tr id="empty-row">
                            <td class="empty" colspan="6">Belum ada item pembelian.</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="number">Total</th>
                            <th id="grand-total" class="number">Rp 0</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </section>

            <div id="hidden-items"></div>

            <div class="actions">
                <button type="submit">Simpan Pembelian</button>
                <a href="{{ route('pembelian.index') }}">Batal</a>
            </div>
        </form>
    </main>

    <script>
        const searchInput = document.getElementById('barang-search');
        const qtyInput = document.getElementById('qty-input');
        const priceInput = document.getElementById('price-input');
        const suggestions = document.getElementById('suggestions');
        const addButton = document.getElementById('add-item');
        const cartBody = document.getElementById('cart-body');
        const hiddenItems = document.getElementById('hidden-items');
        const grandTotal = document.getElementById('grand-total');
        const form = document.getElementById('purchase-form');
        const emptyRow = document.getElementById('empty-row');
        const autocompleteUrl = @json(route('pembelian.autocomplete-barang'));

        let selectedBarang = null;
        let searchTimer = null;
        const cart = new Map();

        const rupiah = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        });

        searchInput.addEventListener('input', () => {
            selectedBarang = null;
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

        addButton.addEventListener('click', addSelectedItem);

        form.addEventListener('submit', (event) => {
            if (cart.size === 0) {
                event.preventDefault();
                alert('Pilih minimal satu barang dulu.');
            }
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
                    <span>${escapeHtml(item.kode_barang)} | HPP ${rupiah.format(item.harga_beli)} | Stok ${formatQty(item.stok)}</span>
                `;
                button.addEventListener('click', () => {
                    selectedBarang = item;
                    searchInput.value = item.nama_barang;
                    priceInput.value = formatQtyForInput(item.harga_beli);
                    suggestions.hidden = true;
                    qtyInput.focus();
                });
                suggestions.appendChild(button);
            });

            suggestions.hidden = false;
        }

        function addSelectedItem() {
            const qty = parseDecimal(qtyInput.value);
            const hargaBeli = parseDecimal(priceInput.value);

            if (! selectedBarang) {
                alert('Pilih barang dari autocomplete dulu.');
                searchInput.focus();
                return;
            }

            if (! Number.isFinite(qty) || qty < 0.001) {
                alert('Qty harus lebih dari 0.');
                qtyInput.focus();
                return;
            }

            if (! Number.isFinite(hargaBeli) || hargaBeli < 0) {
                alert('Harga beli tidak valid.');
                priceInput.focus();
                return;
            }

            const existing = cart.get(selectedBarang.id);
            const nextQty = (existing?.qty ?? 0) + qty;

            cart.set(selectedBarang.id, {
                ...selectedBarang,
                qty: nextQty,
                harga_beli: hargaBeli,
            });

            selectedBarang = null;
            searchInput.value = '';
            qtyInput.value = 1;
            priceInput.value = 0;
            searchInput.focus();
            renderCart();
        }

        function renderCart() {
            cartBody.innerHTML = '';
            hiddenItems.innerHTML = '';

            let total = 0;

            if (cart.size === 0) {
                cartBody.appendChild(emptyRow);
                grandTotal.textContent = rupiah.format(0);
                return;
            }

            cart.forEach((item) => {
                const subtotal = item.harga_beli * item.qty;
                total += subtotal;

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${escapeHtml(item.kode_barang)}</td>
                    <td>${escapeHtml(item.nama_barang)}</td>
                    <td class="number">${formatQty(item.qty)}</td>
                    <td class="number">${rupiah.format(item.harga_beli)}</td>
                    <td class="number">${rupiah.format(subtotal)}</td>
                    <td class="number"><button class="danger-button" type="button" data-remove="${item.id}">Hapus</button></td>
                `;
                cartBody.appendChild(row);

                hiddenItems.insertAdjacentHTML('beforeend', `
                    <input type="hidden" name="barang_id[]" value="${item.id}">
                    <input type="hidden" name="qty[]" value="${formatQtyForInput(item.qty)}">
                    <input type="hidden" name="harga_beli[]" value="${formatQtyForInput(item.harga_beli)}">
                `);
            });

            grandTotal.textContent = rupiah.format(total);

            cartBody.querySelectorAll('[data-remove]').forEach((button) => {
                button.addEventListener('click', () => {
                    cart.delete(Number.parseInt(button.dataset.remove, 10));
                    renderCart();
                });
            });
        }

        function parseDecimal(value) {
            return Number.parseFloat(String(value).replace(',', '.'));
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
</body>
</html>
