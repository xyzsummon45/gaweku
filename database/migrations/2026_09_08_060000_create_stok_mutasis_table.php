<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_mutasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete();
            $table->dateTime('tanggal');
            $table->string('tipe', 50);
            $table->decimal('qty_masuk', 12, 3)->default(0);
            $table->decimal('qty_keluar', 12, 3)->default(0);
            $table->decimal('stok_sebelum', 12, 3);
            $table->decimal('stok_sesudah', 12, 3);
            $table->string('referensi_tipe')->nullable();
            $table->unsignedBigInteger('referensi_id')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['tipe', 'tanggal']);
            $table->index(['referensi_tipe', 'referensi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_mutasis');
    }
};
