<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduksiBiaya extends Model
{
    protected $fillable = [
        'nama_biaya',
        'nominal',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
    ];

    public function produksi()
    {
        return $this->belongsTo(Produksi::class);
    }
}
