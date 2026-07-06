<nav class="top-nav">
    <div class="top-nav-inner">
        <a class="brand" href="{{ route('transaksi.create') }}">T.B Global Jaya</a>
        <div class="nav-links">
            <a class="{{ request()->routeIs('barang.*') ? 'active' : '' }}" href="{{ route('barang.index') }}">Barang</a>
            <a class="{{ request()->routeIs('supplier.*') ? 'active' : '' }}" href="{{ route('supplier.index') }}">Supplier</a>
            <a class="{{ request()->routeIs('pembelian.*') ? 'active' : '' }}" href="{{ route('pembelian.index') }}">Pembelian</a>
            <a class="{{ request()->routeIs('kas.*') ? 'active' : '' }}" href="{{ route('kas.index') }}">Kas</a>
            <a class="{{ request()->routeIs('transaksi.*') ? 'active' : '' }}" href="{{ route('transaksi.index') }}">Transaksi</a>
        </div>
    </div>
</nav>
