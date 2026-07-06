<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KasAccount extends Model
{
    public const KAS_KECIL = 'kas_kecil';
    public const KAS_BESAR = 'kas_besar';
    public const KAS_BANK = 'kas_bank';

    protected $fillable = [
        'kode',
        'nama',
        'saldo',
    ];

    protected $casts = [
        'saldo' => 'decimal:2',
    ];

    public function mutations()
    {
        return $this->hasMany(KasMutation::class);
    }

    public static function bank(): self
    {
        return static::firstOrCreate(
            ['kode' => self::KAS_BANK],
            ['nama' => 'Kas Bank', 'saldo' => 0]
        );
    }
}
