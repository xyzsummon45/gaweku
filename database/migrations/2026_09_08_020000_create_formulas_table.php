<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formulas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_jadi_id')->nullable()->constrained('barangs')->nullOnDelete();
            $table->string('nama_formula');
            $table->decimal('qty_hasil', 15, 3)->default(1);
            $table->decimal('total_biaya', 15, 2)->default(0);
            $table->decimal('hpp', 15, 2)->default(0);
            $table->decimal('margin_persen', 8, 2)->default(0);
            $table->decimal('harga_jual_rekomendasi', 15, 2)->default(0);
            $table->boolean('aktif')->default(true);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formulas');
    }
};
