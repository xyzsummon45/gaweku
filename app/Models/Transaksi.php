<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $fillable = [
        'kode_transaksi',
        'tanggal',
        'total',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'total' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(TransaksiItem::class);
    }

    public function kasMutations()
    {
        return $this->hasMany(KasMutation::class);
    }
}
