<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Supplier</title>
    @include('barang.styles')
</head>
<body>
    @include('layouts.navbar')

    <main class="page">
        <header class="page-header">
            <div>
                <p>Master Supplier</p>
                <h1>Data Supplier</h1>
            </div>
            <a class="primary-button" href="{{ route('supplier.create') }}">Tambah Supplier</a>
        </header>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <section class="panel">
            <form class="search-form" method="GET" action="{{ route('supplier.index') }}">
                <label>
                    <span>Cari Supplier</span>
                    <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama, no HP, atau alamat supplier">
                </label>

                <button type="submit">Cari</button>
                <a class="secondary-button" href="{{ route('supplier.index') }}">Reset</a>
            </form>
        </section>

        <section class="panel table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nama Supplier</th>
                        <th>No HP</th>
                        <th>Alamat</th>
                        <th>Catatan</th>
                        <th class="number">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->nama_supplier }}</td>
                            <td>{{ $supplier->no_hp ?: '-' }}</td>
                            <td>{{ $supplier->alamat ?: '-' }}</td>
                            <td>{{ $supplier->catatan ?: '-' }}</td>
                            <td>
                                <div class="row-actions">
                                    <a class="secondary-button" href="{{ route('supplier.edit', $supplier) }}">Edit</a>
                                    <form method="POST" action="{{ route('supplier.destroy', $supplier) }}" onsubmit="return confirm('Hapus supplier ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="danger-button" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="empty" colspan="5">Belum ada data supplier.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($suppliers->hasPages())
                <div class="pagination">
                    <span>
                        Menampilkan {{ $suppliers->firstItem() }}-{{ $suppliers->lastItem() }}
                        dari {{ $suppliers->total() }} supplier
                    </span>
                    <div class="pagination-links">
                        @if ($suppliers->onFirstPage())
                            <span class="secondary-button">Sebelumnya</span>
                        @else
                            <a class="secondary-button" href="{{ $suppliers->previousPageUrl() }}">Sebelumnya</a>
                        @endif

                        @if ($suppliers->hasMorePages())
                            <a class="secondary-button" href="{{ $suppliers->nextPageUrl() }}">Berikutnya</a>
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
