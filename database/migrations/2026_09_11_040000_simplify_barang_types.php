<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('barangs')
            ->whereIn('jenis_barang', ['barang_dagang', 'bahan_penolong'])
            ->update(['jenis_barang' => 'bahan_baku']);
    }

    public function down(): void
    {
        //
    }
};
