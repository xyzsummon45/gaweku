<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Formula;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormulaTest extends TestCase
{
    use RefreshDatabase;

    public function test_formula_can_be_created(): void
    {
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
            'nama_barang' => 'SEMEN PUTIH',
            'jenis_barang' => 'bahan_baku',
            'satuan' => 'kg',
            'harga_beli' => 20000,
            'harga_jual' => 30000,
            'stok' => 50,
        ]);

        $response = $this->post('/formula', [
            'nama_formula' => 'Formula Tembok A',
            'qty_hasil' => '1',
            'satuan_hasil' => 'm2',
            'margin_persen' => '20',
            'aktif' => '1',
            'barang_bahan_id' => [$bata->id, $semen->id],
            'qty' => ['20', '1'],
        ]);

        $formula = Formula::first();

        $response->assertRedirect(route('formula.show', $formula));
        $this->assertDatabaseHas('formulas', [
            'id' => $formula->id,
            'nama_formula' => 'Formula Tembok A',
            'total_biaya' => 30000,
            'satuan_hasil' => 'm2',
            'hpp' => 30000,
            'margin_persen' => 20,
            'harga_jual_rekomendasi' => 36000,
            'aktif' => true,
        ]);
        $this->assertDatabaseHas('formula_items', [
            'formula_id' => $formula->id,
            'barang_bahan_id' => $bata->id,
            'qty' => 20,
            'harga_beli' => 500,
            'subtotal' => 10000,
        ]);
        $this->assertDatabaseHas('formula_items', [
            'formula_id' => $formula->id,
            'barang_bahan_id' => $semen->id,
            'qty' => 1,
            'harga_beli' => 20000,
            'subtotal' => 20000,
        ]);
    }

    public function test_formula_can_be_updated(): void
    {
        $bata = Barang::create([
            'kode_barang' => 'BB001',
            'nama_barang' => 'BATA MERAH',
            'jenis_barang' => 'bahan_baku',
            'satuan' => 'pcs',
            'harga_beli' => 500,
            'harga_jual' => 1000,
            'stok' => 100,
        ]);

        $formula = Formula::create([
            'nama_formula' => 'Formula Lama',
            'qty_hasil' => 1,
            'satuan_hasil' => 'm2',
            'total_biaya' => 10000,
            'hpp' => 10000,
            'margin_persen' => 0,
            'harga_jual_rekomendasi' => 10000,
            'aktif' => true,
        ]);

        $this->put(route('formula.update', $formula), [
            'nama_formula' => 'Formula Baru',
            'qty_hasil' => '2',
            'satuan_hasil' => 'm2',
            'margin_persen' => '10',
            'aktif' => '1',
            'barang_bahan_id' => [$bata->id],
            'qty' => ['20'],
        ])->assertRedirect(route('formula.show', $formula));

        $this->assertDatabaseHas('formulas', [
            'id' => $formula->id,
            'nama_formula' => 'Formula Baru',
            'qty_hasil' => 2,
            'satuan_hasil' => 'm2',
            'total_biaya' => 10000,
            'hpp' => 5000,
            'harga_jual_rekomendasi' => 5500,
        ]);
    }

    public function test_formula_can_use_trade_goods_as_material(): void
    {
        $pipa = Barang::create([
            'kode_barang' => 'KAS123',
            'nama_barang' => 'PIPA PVC 80cm',
            'jenis_barang' => 'barang_dagang',
            'satuan' => 'pcs',
            'harga_beli' => 17000,
            'harga_jual' => 20000,
            'stok' => 6,
        ]);

        $response = $this->post('/formula', [
            'nama_formula' => 'Formula Tembok A',
            'qty_hasil' => '1',
            'satuan_hasil' => 'm2',
            'margin_persen' => '10',
            'aktif' => '1',
            'barang_bahan_id' => [$pipa->id],
            'qty' => ['2'],
        ]);

        $formula = Formula::first();

        $response->assertRedirect(route('formula.show', $formula));
        $this->assertDatabaseHas('formula_items', [
            'formula_id' => $formula->id,
            'barang_bahan_id' => $pipa->id,
            'qty' => 2,
            'subtotal' => 34000,
        ]);
    }
}
