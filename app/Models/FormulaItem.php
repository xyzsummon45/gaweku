<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormulaItem extends Model
{
    protected $fillable = [
        'barang_bahan_id',
        'kode_barang',
        'nama_barang',
        'satuan',
        'qty',
        'harga_beli',
        'subtotal',
    ];

    protected $casts = [
        'qty' => 'decimal:3',
        'harga_beli' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function formula()
    {
        return $this->belongsTo(Formula::class);
    }

    public function barangBahan()
    {
        return $this->belongsTo(Barang::class, 'barang_bahan_id');
    }
}
