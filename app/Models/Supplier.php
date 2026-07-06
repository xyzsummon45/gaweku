<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'nama_supplier',
        'no_hp',
        'alamat',
        'catatan',
    ];

    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }
}
