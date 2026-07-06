<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $fillable = [
        'supplier_id',
        'kode_pembelian',
        'nomor_invoice',
        'tanggal',
        'tanggal_jatuh_tempo',
        'total',
        'jumlah_dibayar',
        'status_pembayaran',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'total' => 'decimal:2',
        'jumlah_dibayar' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PembelianItem::class);
    }

    public function kasMutations()
    {
        return $this->hasMany(KasMutation::class);
    }

    public function sisaHutang(): float
    {
        return max(0, (float) $this->total - (float) $this->jumlah_dibayar);
    }
}
