<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembelianItem extends Model
{
    protected $fillable = [
        'pembelian_id',
        'barang_id',
        'kode_barang',
        'nama_barang',
        'qty',
        'harga_beli',
        'subtotal',
    ];

    protected $casts = [
        'qty' => 'decimal:3',
        'harga_beli' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
