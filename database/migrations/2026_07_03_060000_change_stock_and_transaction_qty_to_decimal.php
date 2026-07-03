<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE barangs MODIFY stok DECIMAL(15, 3) NOT NULL DEFAULT 0');
            DB::statement('ALTER TABLE transaksi_items MODIFY qty DECIMAL(15, 3) NOT NULL');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE barangs MODIFY stok INT NOT NULL DEFAULT 0');
            DB::statement('ALTER TABLE transaksi_items MODIFY qty INT NOT NULL');
        }
    }
};
