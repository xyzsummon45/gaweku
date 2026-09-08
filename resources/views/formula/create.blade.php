<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Formula</title>
    @include('barang.styles')
</head>
<body>
    @include('layouts.navbar')

    <main class="page">
        <header class="page-header">
            <div>
                <p>Master Produksi</p>
                <h1>Tambah Formula</h1>
            </div>
        </header>

        <form method="POST" action="{{ route('formula.store') }}" id="formula-form">
            @include('formula._form', ['submit' => 'Simpan Formula'])
        </form>
    </main>
</body>
</html>
