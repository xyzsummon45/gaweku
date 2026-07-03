<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang</title>
    @include('barang.styles')
</head>
<body>
    @include('layouts.navbar')

    <main class="page">
        <header class="page-header">
            <div>
                <p>Master Barang</p>
                <h1>Edit Barang</h1>
            </div>
        </header>

        <section class="panel">
            <form method="POST" action="{{ route('barang.update', $barang) }}">
                @method('PUT')
                @include('barang._form', ['submit' => 'Update Barang'])
            </form>
        </section>
    </main>
</body>
</html>
