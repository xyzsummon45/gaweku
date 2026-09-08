<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formula_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formula_id')->constrained('formulas')->cascadeOnDelete();
            $table->foreignId('barang_bahan_id')->constrained('barangs')->restrictOnDelete();
            $table->string('kode_barang');
            $table->string('nama_barang');
            $table->string('satuan', 30);
            $table->decimal('qty', 15, 3);
            $table->decimal('harga_beli', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formula_items');
    }
};
