<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('formulas', function (Blueprint $table) {
            $table->dropForeign(['barang_jadi_id']);
            $table->dropUnique(['barang_jadi_id']);
            $table->foreignId('barang_jadi_id')->nullable()->change();
            $table->foreign('barang_jadi_id')->references('id')->on('barangs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('formulas', function (Blueprint $table) {
            $table->dropForeign(['barang_jadi_id']);
            $table->foreignId('barang_jadi_id')->nullable(false)->change();
            $table->unique('barang_jadi_id');
            $table->foreign('barang_jadi_id')->references('id')->on('barangs')->restrictOnDelete();
        });
    }
};
