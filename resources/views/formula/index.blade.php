<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Formula</title>
    @include('barang.styles')
</head>
<body>
    @include('layouts.navbar')

    <main class="page">
        <header class="page-header">
            <div>
                <p>Master Produksi</p>
                <h1>Data Formula</h1>
            </div>
            <a class="primary-button" href="{{ route('formula.create') }}">Tambah Formula</a>
        </header>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <section class="panel">
            <form class="search-form" method="GET" action="{{ route('formula.index') }}">
                <label>
                    <span>Cari Formula</span>
                    <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama formula atau produk jadi">
                </label>

                <button type="submit">Cari</button>
                <a class="secondary-button" href="{{ route('formula.index') }}">Reset</a>
            </form>
        </section>

        <section class="panel table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Formula</th>
                        <th>Produk Jadi</th>
                        <th class="number">Hasil</th>
                        <th class="number">Bahan</th>
                        <th class="number">HPP</th>
                        <th class="number">Margin</th>
                        <th class="number">Rekomendasi Jual</th>
                        <th>Status</th>
                        <th class="number">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($formulas as $formula)
                        <tr>
                            <td>{{ $formula->nama_formula }}</td>
                            <td>{{ $formula->barangJadi->nama_barang }}</td>
                            <td class="number">{{ rtrim(rtrim(number_format($formula->qty_hasil, 3, ',', '.'), '0'), ',') }} {{ $formula->barangJadi->satuan }}</td>
                            <td class="number">{{ $formula->items_count }}</td>
                            <td class="number">Rp {{ number_format($formula->hpp, 0, ',', '.') }}</td>
                            <td class="number">{{ rtrim(rtrim(number_format($formula->margin_persen, 2, ',', '.'), '0'), ',') }}%</td>
                            <td class="number">Rp {{ number_format($formula->harga_jual_rekomendasi, 0, ',', '.') }}</td>
                            <td>{{ $formula->aktif ? 'Aktif' : 'Nonaktif' }}</td>
                            <td>
                                <div class="row-actions">
                                    <a class="secondary-button" href="{{ route('formula.show', $formula) }}">Detail</a>
                                    <a class="secondary-button" href="{{ route('formula.edit', $formula) }}">Edit</a>
                                    <form method="POST" action="{{ route('formula.destroy', $formula) }}" onsubmit="return confirm('Hapus formula ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="danger-button" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="empty" colspan="9">Belum ada data formula.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($formulas->hasPages())
                <div class="pagination">
                    <span>
                        Menampilkan {{ $formulas->firstItem() }}-{{ $formulas->lastItem() }}
                        dari {{ $formulas->total() }} formula
                    </span>
                    <div class="pagination-links">
                        @if ($formulas->onFirstPage())
                            <span class="secondary-button">Sebelumnya</span>
                        @else
                            <a class="secondary-button" href="{{ $formulas->previousPageUrl() }}">Sebelumnya</a>
                        @endif

                        @if ($formulas->hasMorePages())
                            <a class="secondary-button" href="{{ $formulas->nextPageUrl() }}">Berikutnya</a>
                        @else
                            <span class="secondary-button">Berikutnya</span>
                        @endif
                    </div>
                </div>
            @endif
        </section>
    </main>
</body>
</html>
