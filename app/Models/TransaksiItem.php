<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiItem extends Model
{
    protected $fillable = [
        'transaksi_id',
        'barang_id',
        'kode_barang',
        'nama_barang',
        'harga_jual',
        'harga_modal',
        'qty',
        'subtotal',
        'subtotal_modal',
        'laba_kotor',
    ];

    protected $casts = [
        'harga_jual' => 'decimal:2',
        'harga_modal' => 'decimal:2',
        'qty' => 'decimal:3',
        'subtotal' => 'decimal:2',
        'subtotal_modal' => 'decimal:2',
        'laba_kotor' => 'decimal:2',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
