<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('formulas')
            ->orderBy('id')
            ->each(function ($formula) {
                $recommendation = $this->roundUpToThousand((float) $formula->hpp + ((float) $formula->hpp * (float) $formula->margin_persen / 100));

                DB::table('formulas')
                    ->where('id', $formula->id)
                    ->update(['harga_jual_rekomendasi' => $recommendation]);
            });

        DB::table('produksis')
            ->orderBy('id')
            ->each(function ($produksi) {
                $recommendation = $this->roundUpToThousand((float) $produksi->hpp + ((float) $produksi->hpp * (float) $produksi->margin_persen / 100));

                DB::table('produksis')
                    ->where('id', $produksi->id)
                    ->update(['harga_jual_rekomendasi' => $recommendation]);
            });
    }

    public function down(): void
    {
        //
    }

    private function roundUpToThousand(float $value): float
    {
        return ceil($value / 1000) * 1000;
    }
};
