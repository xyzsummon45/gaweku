<?php

namespace Tests\Feature;

use App\Models\Barang;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockOpnameTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_opname_updates_stock_and_records_history(): void
    {
        $barang = Barang::create([
            'kode_barang' => 'AAA11',
            'nama_barang' => 'SEMEN PUTIH',
            'jenis_barang' => 'bahan_baku',
            'satuan' => 'kg',
            'harga_beli' => 20000,
            'harga_jual' => 30000,
            'stok' => 8,
        ]);

        $this->post('/stock-opname', [
            'barang_id' => $barang->id,
            'tanggal' => '2026-09-08 15:30:00',
            'stok_fisik' => '4,5',
            'catatan' => 'Opname gudang',
        ])->assertRedirect('/stock-opname');

        $this->assertSame('4.500', $barang->fresh()->stok);
        $this->assertDatabaseHas('stok_mutasis', [
            'barang_id' => $barang->id,
            'tipe' => 'opname',
            'qty_masuk' => 0,
            'qty_keluar' => 3.5,
            'stok_sebelum' => 8,
            'stok_sesudah' => 4.5,
            'referensi_tipe' => 'stock_opname',
            'catatan' => 'Opname gudang',
        ]);
    }
}
