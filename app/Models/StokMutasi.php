<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokMutasi extends Model
{
    protected $fillable = [
        'barang_id',
        'tanggal',
        'tipe',
        'qty_masuk',
        'qty_keluar',
        'stok_sebelum',
        'stok_sesudah',
        'referensi_tipe',
        'referensi_id',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'qty_masuk' => 'decimal:3',
        'qty_keluar' => 'decimal:3',
        'stok_sebelum' => 'decimal:3',
        'stok_sesudah' => 'decimal:3',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
