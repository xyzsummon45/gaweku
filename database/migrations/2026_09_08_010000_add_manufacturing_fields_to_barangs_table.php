<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->string('jenis_barang')->default('bahan_baku')->after('nama_barang');
            $table->string('satuan', 30)->default('pcs')->after('jenis_barang');
        });
    }

    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn(['jenis_barang', 'satuan']);
        });
    }
};
