<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Supplier</title>
    @include('barang.styles')
</head>
<body>
    @include('layouts.navbar')

    <main class="page">
        <header class="page-header">
            <div>
                <p>Master Supplier</p>
                <h1>Edit Supplier</h1>
            </div>
        </header>

        <section class="panel">
            <form method="POST" action="{{ route('supplier.update', $supplier) }}">
                @method('PUT')
                @include('supplier._form', ['submit' => 'Update Supplier'])
            </form>
        </section>
    </main>
</body>
</html>
