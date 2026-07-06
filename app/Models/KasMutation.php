<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KasMutation extends Model
{
    protected $fillable = [
        'kas_account_id',
        'related_kas_account_id',
        'pembelian_id',
        'transaksi_id',
        'tanggal',
        'jenis',
        'jumlah',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'jumlah' => 'decimal:2',
    ];

    public function kasAccount()
    {
        return $this->belongsTo(KasAccount::class);
    }

    public function relatedKasAccount()
    {
        return $this->belongsTo(KasAccount::class, 'related_kas_account_id');
    }

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }
}
