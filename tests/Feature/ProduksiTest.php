<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Formula;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProduksiTest extends TestCase
{
    use RefreshDatabase;

    public function test_production_consumes_materials_and_adds_finished_goods(): void
    {
        $tembok = Barang::create([
            'kode_barang' => 'BJ001',
            'nama_barang' => 'TEMBOK',
            'jenis_barang' => 'barang_dagang',
            'satuan' => 'm2',
            'harga_beli' => 0,
            'harga_jual' => 50000,
            'stok' => 0,
        ]);

        $bata = Barang::create([
            'kode_barang' => 'BB001',
            'nama_barang' => 'BATA MERAH',
            'jenis_barang' => 'bahan_baku',
            'satuan' => 'pcs',
            'harga_beli' => 500,
            'harga_jual' => 1000,
            'stok' => 100,
        ]);

        $semen = Barang::create([
            'kode_barang' => 'BB002',
            'nama_barang' => 'SEMEN',
            'jenis_barang' => 'bahan_baku',
            'satuan' => 'kg',
            'harga_beli' => 20000,
            'harga_jual' => 30000,
            'stok' => 20,
        ]);

        $formula = Formula::create([
            'nama_formula' => 'Formula Tembok 1 m2',
            'qty_hasil' => 1,
            'satuan_hasil' => 'm2',
            'total_biaya' => 30000,
            'hpp' => 30000,
            'margin_persen' => 0,
            'harga_jual_rekomendasi' => 30000,
            'aktif' => true,
        ]);
        $formula->items()->create([
            'barang_bahan_id' => $bata->id,
            'kode_barang' => $bata->kode_barang,
            'nama_barang' => $bata->nama_barang,
            'satuan' => $bata->satuan,
            'qty' => 20,
            'harga_beli' => 500,
            'subtotal' => 10000,
        ]);
        $formula->items()->create([
            'barang_bahan_id' => $semen->id,
            'kode_barang' => $semen->kode_barang,
            'nama_barang' => $semen->nama_barang,
            'satuan' => $semen->satuan,
            'qty' => 1,
            'harga_beli' => 20000,
            'subtotal' => 20000,
        ]);

        $response = $this->post('/produksi', [
            'formula_id' => $formula->id,
            'barang_hasil_id' => $tembok->id,
            'tanggal' => '2026-09-08 10:00:00',
            'qty_produksi' => '2,5',
            'catatan' => 'Batch pagi',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('produksis', [
            'formula_id' => $formula->id,
            'barang_hasil_id' => $tembok->id,
            'nama_formula' => 'Formula Tembok 1 m2',
            'qty_produksi' => 2.5,
            'total_biaya' => 75000,
            'hpp' => 30000,
        ]);
        $this->assertDatabaseHas('produksi_items', [
            'barang_bahan_id' => $bata->id,
            'qty_pakai' => 50,
            'subtotal' => 25000,
        ]);
        $this->assertDatabaseHas('produksi_items', [
            'barang_bahan_id' => $semen->id,
            'qty_pakai' => 2.5,
            'subtotal' => 50000,
        ]);
        $this->assertSame('50.000', $bata->fresh()->stok);
        $this->assertSame('17.500', $semen->fresh()->stok);
        $this->assertSame('2.500', $tembok->fresh()->stok);
        $this->assertSame('barang_jadi', $tembok->fresh()->jenis_barang);
        $this->assertSame('30000.00', $tembok->fresh()->harga_beli);
    }

    public function test_production_is_rejected_when_material_stock_is_not_enough(): void
    {
        $tembok = Barang::create([
            'kode_barang' => 'BJ001',
            'nama_barang' => 'TEMBOK',
            'jenis_barang' => 'barang_dagang',
            'satuan' => 'm2',
            'harga_beli' => 0,
            'harga_jual' => 50000,
            'stok' => 0,
        ]);

        $bata = Barang::create([
            'kode_barang' => 'BB001',
            'nama_barang' => 'BATA MERAH',
            'jenis_barang' => 'bahan_baku',
            'satuan' => 'pcs',
            'harga_beli' => 500,
            'harga_jual' => 1000,
            'stok' => 10,
        ]);

        $formula = Formula::create([
            'nama_formula' => 'Formula Tembok 1 m2',
            'qty_hasil' => 1,
            'satuan_hasil' => 'm2',
            'total_biaya' => 10000,
            'hpp' => 10000,
            'margin_persen' => 0,
            'harga_jual_rekomendasi' => 10000,
            'aktif' => true,
        ]);
        $formula->items()->create([
            'barang_bahan_id' => $bata->id,
            'kode_barang' => $bata->kode_barang,
            'nama_barang' => $bata->nama_barang,
            'satuan' => $bata->satuan,
            'qty' => 20,
            'harga_beli' => 500,
            'subtotal' => 10000,
        ]);

        $this->post('/produksi', [
            'formula_id' => $formula->id,
            'barang_hasil_id' => $tembok->id,
            'tanggal' => '2026-09-08 10:00:00',
            'qty_produksi' => '1',
        ])->assertSessionHasErrors('formula_id');

        $this->assertDatabaseCount('produksis', 0);
        $this->assertSame('10.000', $bata->fresh()->stok);
        $this->assertSame('0.000', $tembok->fresh()->stok);
    }
}
