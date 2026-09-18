<nav class="top-nav">
    <div class="top-nav-inner">
        <a class="brand" href="{{ route('transaksi.create') }}">T.B Global Jaya</a>
        <div class="nav-links">
            <a class="{{ request()->routeIs('barang.*') ? 'active' : '' }}" href="{{ route('barang.index') }}">Barang</a>
            <a class="{{ request()->routeIs('kartu-stok.*') ? 'active' : '' }}" href="{{ route('kartu-stok.index') }}">Kartu Stok</a>
            <a class="{{ request()->routeIs('stock-opname.*') ? 'active' : '' }}" href="{{ route('stock-opname.index') }}">Stock Opname</a>
            <a class="{{ request()->routeIs('supplier.*') ? 'active' : '' }}" href="{{ route('supplier.index') }}">Supplier</a>
            <a class="{{ request()->routeIs('pembelian.*') ? 'active' : '' }}" href="{{ route('pembelian.index') }}">Pembelian</a>
            <a class="{{ request()->routeIs('kas.*') ? 'active' : '' }}" href="{{ route('kas.index') }}">Kas</a>
            <a class="{{ request()->routeIs('transaksi.*') ? 'active' : '' }}" href="{{ route('transaksi.index') }}">Transaksi</a>
            <a class="{{ request()->routeIs('laporan.*') ? 'active' : '' }}" href="{{ route('laporan.penjualan') }}">Laporan</a>
        </div>
    </div>
</nav>
