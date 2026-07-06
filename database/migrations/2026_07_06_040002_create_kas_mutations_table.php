<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kas_mutations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kas_account_id')->constrained('kas_accounts')->restrictOnDelete();
            $table->foreignId('related_kas_account_id')->nullable()->constrained('kas_accounts')->nullOnDelete();
            $table->foreignId('pembelian_id')->nullable()->constrained('pembelians')->nullOnDelete();
            $table->foreignId('transaksi_id')->nullable()->constrained('transaksis')->nullOnDelete();
            $table->dateTime('tanggal');
            $table->enum('jenis', ['pemasukan', 'pengeluaran', 'mutasi_masuk', 'mutasi_keluar', 'pembayaran_hutang']);
            $table->decimal('jumlah', 15, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kas_mutations');
    }
};
