<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produksis', function (Blueprint $table) {
            $table->decimal('margin_persen', 8, 2)->default(0)->after('hpp');
            $table->decimal('harga_jual_rekomendasi', 15, 2)->default(0)->after('margin_persen');
            $table->decimal('harga_jual_diterapkan', 15, 2)->nullable()->after('harga_jual_rekomendasi');
            $table->timestamp('harga_jual_diterapkan_at')->nullable()->after('harga_jual_diterapkan');
        });

        DB::table('produksis')
            ->orderBy('id')
            ->each(function ($produksi) {
                $formula = DB::table('formulas')->where('id', $produksi->formula_id)->first();

                if (! $formula) {
                    return;
                }

                $margin = (float) $formula->margin_persen;
                $hpp = (float) $produksi->hpp;

                DB::table('produksis')
                    ->where('id', $produksi->id)
                    ->update([
                        'margin_persen' => $margin,
                        'harga_jual_rekomendasi' => $hpp + ($hpp * $margin / 100),
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('produksis', function (Blueprint $table) {
            $table->dropColumn([
                'margin_persen',
                'harga_jual_rekomendasi',
                'harga_jual_diterapkan',
                'harga_jual_diterapkan_at',
            ]);
        });
    }
};
