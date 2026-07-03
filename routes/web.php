<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\TransaksiController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/barang/import', [BarangController::class, 'import'])->name('barang.import');
Route::resource('barang', BarangController::class)->except('show');

Route::get('/transaksi/autocomplete', [TransaksiController::class, 'autocomplete'])->name('transaksi.autocomplete');
Route::resource('transaksi', TransaksiController::class)->only(['index', 'create', 'store', 'show']);
