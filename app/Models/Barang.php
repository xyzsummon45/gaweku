<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'jenis_barang',
        'satuan',
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

    public function formulaProdukJadi()
    {
        return $this->hasOne(Formula::class, 'barang_jadi_id');
    }

    public function formulaItemsBahan()
    {
        return $this->hasMany(FormulaItem::class, 'barang_bahan_id');
    }
}
