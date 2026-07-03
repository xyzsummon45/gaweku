<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang</title>
    @include('barang.styles')
</head>
<body>
    @include('layouts.navbar')

    <main class="page">
        <header class="page-header">
            <div>
                <p>Master Barang</p>
                <h1>Tambah Barang</h1>
            </div>
        </header>

        <section class="panel">
            <form method="POST" action="{{ route('barang.store') }}">
                @include('barang._form', ['submit' => 'Simpan Barang'])
            </form>
        </section>
    </main>
</body>
</html>
