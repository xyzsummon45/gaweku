<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Formula</title>
    @include('barang.styles')
</head>
<body>
    @include('layouts.navbar')

    <main class="page">
        <header class="page-header">
            <div>
                <p>Master Produksi</p>
                <h1>Edit Formula</h1>
            </div>
        </header>

        <form method="POST" action="{{ route('formula.update', $formula) }}" id="formula-form">
            @method('PUT')
            @include('formula._form', ['submit' => 'Update Formula'])
        </form>
    </main>
</body>
</html>
