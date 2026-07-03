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

    public function transaksiItems()
    {
        return $this->hasMany(TransaksiItem::class);
    }
}
