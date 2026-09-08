<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduksiItem extends Model
{
    protected $fillable = [
        'barang_bahan_id',
        'kode_barang',
        'nama_barang',
        'satuan',
        'qty_formula',
        'qty_pakai',
        'harga_beli',
        'subtotal',
    ];

    protected $casts = [
        'qty_formula' => 'decimal:3',
        'qty_pakai' => 'decimal:3',
        'harga_beli' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function produksi()
    {
        return $this->belongsTo(Produksi::class);
    }

    public function barangBahan()
    {
        return $this->belongsTo(Barang::class, 'barang_bahan_id');
    }
}
