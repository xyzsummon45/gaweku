<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'harga_beli',
        'harga_jual',
        'stok',
    ];

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'stok' => 'decimal:3',
    ];

    public function transaksiItems()
    {
        return $this->hasMany(TransaksiItem::class);
    }

    public function pembelianItems()
    {
        return $this->hasMany(PembelianItem::class);
    }
}
