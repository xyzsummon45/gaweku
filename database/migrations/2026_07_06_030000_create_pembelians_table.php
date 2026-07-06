<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembelians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            $table->string('kode_pembelian')->unique();
            $table->string('nomor_invoice')->unique();
            $table->date('tanggal');
            $table->date('tanggal_jatuh_tempo');
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('status_pembayaran', ['belum_lunas', 'sebagian', 'lunas'])->default('belum_lunas');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelians');
    }
};
