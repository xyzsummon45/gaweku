<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kas_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->decimal('saldo', 15, 2)->default(0);
            $table->timestamps();
        });

        DB::table('kas_accounts')->insert([
            [
                'kode' => 'kas_kecil',
                'nama' => 'Kas Kecil',
                'saldo' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'kas_besar',
                'nama' => 'Kas Besar',
                'saldo' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'kas_bank',
                'nama' => 'Kas Bank',
                'saldo' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('kas_accounts');
    }
};
