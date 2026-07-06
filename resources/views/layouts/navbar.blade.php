<nav class="top-nav">
    <div class="top-nav-inner">
        <a class="brand" href="{{ route('transaksi.create') }}">Jual Beli</a>
        <div class="nav-links">
            <a class="{{ request()->routeIs('barang.*') ? 'active' : '' }}" href="{{ route('barang.index') }}">Barang</a>
            <a class="{{ request()->routeIs('supplier.*') ? 'active' : '' }}" href="{{ route('supplier.index') }}">Supplier</a>
            <a class="{{ request()->routeIs('transaksi.index') || request()->routeIs('transaksi.show') ? 'active' : '' }}" href="{{ route('transaksi.index') }}">Riwayat Transaksi</a>
            <a class="{{ request()->routeIs('transaksi.create') ? 'active' : '' }}" href="{{ route('transaksi.create') }}">Transaksi Baru</a>
        </div>
    </div>
</nav>
