<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Stok</title>
    @include('barang.styles')
</head>
<body>
    @include('layouts.navbar')

    <main class="page">
        <header class="page-header">
            <div>
                <p>Inventory</p>
                <h1>Kartu Stok</h1>
            </div>
        </header>

        @if ($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <section class="panel">
            <form class="filter-form" method="GET" action="{{ route('kartu-stok.index') }}">
                <label>
                    <span>Barang</span>
                    <div class="item-search">
                        <input
                            id="barang-search"
                            type="text"
                            value="{{ $barang ? $barang->kode_barang.' - '.$barang->nama_barang : '' }}"
                            placeholder="Ketik minimal 2 huruf, contoh: semen"
                            autocomplete="off"
                        >
                        <input id="barang-id" type="hidden" name="barang_id" value="{{ $filters['barang_id'] }}">
                        <div id="suggestions" class="suggestions" hidden></div>
                    </div>
                </label>

                <label>
                    <span>Dari Tanggal</span>
                    <input type="date" name="tanggal_mulai" value="{{ $filters['tanggal_mulai'] }}">
                </label>

                <label>
                    <span>Sampai Tanggal</span>
                    <input type="date" name="tanggal_selesai" value="{{ $filters['tanggal_selesai'] }}">
                </label>

                <button type="submit">Cari</button>
                <a class="secondary-button" href="{{ route('kartu-stok.index') }}">Reset</a>
            </form>
        </section>

        @if ($barang)
            <section class="panel summary-grid">
                <div>
                    <span>Barang</span>
                    <strong>{{ $barang->kode_barang }} - {{ $barang->nama_barang }}</strong>
                </div>
                <div>
                    <span>Stok Awal Periode</span>
                    <strong>{{ rtrim(rtrim(number_format($stokAwal, 3, ',', '.'), '0'), ',') }} {{ $barang->satuan }}</strong>
                </div>
                <div>
                    <span>Stok Akhir Periode</span>
                    <strong>{{ rtrim(rtrim(number_format($stokAkhir, 3, ',', '.'), '0'), ',') }} {{ $barang->satuan }}</strong>
                </div>
            </section>

            <section class="panel table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Referensi</th>
                            <th>Catatan</th>
                            <th class="number">Masuk</th>
                            <th class="number">Keluar</th>
                            <th class="number">Stok Sebelum</th>
                            <th class="number">Stok Sesudah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mutasis as $mutasi)
                            <tr>
                                <td>{{ $mutasi->tanggal->format('d/m/Y H:i') }}</td>
                                <td>{{ ucwords(str_replace('_', ' ', $mutasi->tipe)) }}</td>
                                <td>{{ $mutasi->referensi_tipe ? ucwords(str_replace('_', ' ', $mutasi->referensi_tipe)).' #'.$mutasi->referensi_id : '-' }}</td>
                                <td>{{ $mutasi->catatan ?: '-' }}</td>
                                <td class="number">{{ $mutasi->qty_masuk > 0 ? rtrim(rtrim(number_format($mutasi->qty_masuk, 3, ',', '.'), '0'), ',').' '.$barang->satuan : '-' }}</td>
                                <td class="number">{{ $mutasi->qty_keluar > 0 ? rtrim(rtrim(number_format($mutasi->qty_keluar, 3, ',', '.'), '0'), ',').' '.$barang->satuan : '-' }}</td>
                                <td class="number">{{ rtrim(rtrim(number_format($mutasi->stok_sebelum, 3, ',', '.'), '0'), ',') }} {{ $barang->satuan }}</td>
                                <td class="number">{{ rtrim(rtrim(number_format($mutasi->stok_sesudah, 3, ',', '.'), '0'), ',') }} {{ $barang->satuan }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="empty" colspan="8">Belum ada mutasi stok pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>
        @else
            <section class="panel">
                Pilih barang dan periode tanggal untuk melihat kartu stok.
            </section>
        @endif
    </main>

    <script>
        const searchInput = document.getElementById('barang-search');
        const barangIdInput = document.getElementById('barang-id');
        const suggestions = document.getElementById('suggestions');
        const autocompleteUrl = @json(route('kartu-stok.autocomplete-barang'));
        let searchTimer = null;

        searchInput.addEventListener('input', () => {
            barangIdInput.value = '';
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
                    barangIdInput.value = item.id;
                    searchInput.value = `${item.kode_barang} - ${item.nama_barang}`;
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
