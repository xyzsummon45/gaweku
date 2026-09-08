<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formula extends Model
{
    protected $fillable = [
        'barang_jadi_id',
        'nama_formula',
        'qty_hasil',
        'total_biaya',
        'hpp',
        'margin_persen',
        'harga_jual_rekomendasi',
        'aktif',
        'catatan',
    ];

    protected $casts = [
        'qty_hasil' => 'decimal:3',
        'total_biaya' => 'decimal:2',
        'hpp' => 'decimal:2',
        'margin_persen' => 'decimal:2',
        'harga_jual_rekomendasi' => 'decimal:2',
        'aktif' => 'boolean',
    ];

    public function barangJadi()
    {
        return $this->belongsTo(Barang::class, 'barang_jadi_id');
    }

    public function items()
    {
        return $this->hasMany(FormulaItem::class);
    }
}
