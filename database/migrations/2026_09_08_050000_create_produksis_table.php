<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formula_id')->constrained('formulas')->restrictOnDelete();
            $table->foreignId('barang_hasil_id')->constrained('barangs')->restrictOnDelete();
            $table->string('kode_produksi')->unique();
            $table->dateTime('tanggal');
            $table->string('nama_formula');
            $table->decimal('qty_formula_hasil', 15, 3);
            $table->string('satuan_hasil', 30);
            $table->decimal('qty_produksi', 15, 3);
            $table->decimal('total_biaya', 15, 2);
            $table->decimal('hpp', 15, 2);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produksis');
    }
};
