<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_items', function (Blueprint $table) {
            $table->decimal('harga_modal', 15, 2)->default(0)->after('harga_jual');
            $table->decimal('subtotal_modal', 15, 2)->default(0)->after('subtotal');
            $table->decimal('laba_kotor', 15, 2)->default(0)->after('subtotal_modal');
        });

        DB::table('transaksi_items')
            ->leftJoin('barangs', 'transaksi_items.barang_id', '=', 'barangs.id')
            ->select([
                'transaksi_items.id',
                'transaksi_items.qty',
                'transaksi_items.subtotal',
                'barangs.harga_beli',
            ])
            ->orderBy('transaksi_items.id')
            ->each(function ($item) {
                $hargaModal = (float) ($item->harga_beli ?? 0);
                $subtotalModal = $hargaModal * (float) $item->qty;

                DB::table('transaksi_items')
                    ->where('id', $item->id)
                    ->update([
                        'harga_modal' => $hargaModal,
                        'subtotal_modal' => $subtotalModal,
                        'laba_kotor' => (float) $item->subtotal - $subtotalModal,
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('transaksi_items', function (Blueprint $table) {
            $table->dropColumn(['harga_modal', 'subtotal_modal', 'laba_kotor']);
        });
    }
};
