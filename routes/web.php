<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransaksiController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/barang/import', [BarangController::class, 'import'])->name('barang.import');
Route::resource('barang', BarangController::class)->except('show');

Route::resource('supplier', SupplierController::class)->except('show');

Route::get('/pembelian/autocomplete-barang', [PembelianController::class, 'autocompleteBarang'])->name('pembelian.autocomplete-barang');
Route::resource('pembelian', PembelianController::class)->only(['index', 'create', 'store', 'show']);

Route::get('/transaksi/autocomplete', [TransaksiController::class, 'autocomplete'])->name('transaksi.autocomplete');
Route::resource('transaksi', TransaksiController::class)->only(['index', 'create', 'store', 'show']);
