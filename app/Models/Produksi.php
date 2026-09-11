<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produksi extends Model
{
    protected $fillable = [
        'formula_id',
        'barang_hasil_id',
        'kode_produksi',
        'tanggal',
        'nama_formula',
        'qty_formula_hasil',
        'satuan_hasil',
        'qty_produksi',
        'total_biaya',
        'hpp',
        'margin_persen',
        'harga_jual_rekomendasi',
        'harga_jual_diterapkan',
        'harga_jual_diterapkan_at',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'qty_formula_hasil' => 'decimal:3',
        'qty_produksi' => 'decimal:3',
        'total_biaya' => 'decimal:2',
        'hpp' => 'decimal:2',
        'margin_persen' => 'decimal:2',
        'harga_jual_rekomendasi' => 'decimal:2',
        'harga_jual_diterapkan' => 'decimal:2',
        'harga_jual_diterapkan_at' => 'datetime',
    ];

    public function formula()
    {
        return $this->belongsTo(Formula::class);
    }

    public function barangHasil()
    {
        return $this->belongsTo(Barang::class, 'barang_hasil_id');
    }

    public function items()
    {
        return $this->hasMany(ProduksiItem::class);
    }

    public function biayas()
    {
        return $this->hasMany(ProduksiBiaya::class);
    }
}
