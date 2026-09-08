<?php

namespace Tests\Feature;

use App\Models\Barang;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarangTest extends TestCase
{
    use RefreshDatabase;

    public function test_barang_list_can_be_searched_by_name_or_code(): void
    {
        Barang::create([
            'kode_barang' => 'AAA11',
            'nama_barang' => 'SEMEN PUTIH',
            'jenis_barang' => 'bahan_baku',
            'satuan' => 'kg',
            'harga_beli' => 20000,
            'harga_jual' => 30000,
            'stok' => 24.3,
        ]);

        Barang::create([
            'kode_barang' => 'KAS123',
            'nama_barang' => 'PIPA PVC 80cm',
            'jenis_barang' => 'barang_dagang',
            'satuan' => 'pcs',
            'harga_beli' => 17000,
            'harga_jual' => 20000,
            'stok' => 6,
        ]);

        $this->get('/barang?q=semen')
            ->assertOk()
            ->assertSee('SEMEN PUTIH')
            ->assertSee('Bahan Baku')
            ->assertSee('kg')
            ->assertSee('24,3')
            ->assertDontSee('PIPA PVC 80cm');

        $this->get('/barang?q=KAS123')
            ->assertOk()
            ->assertSee('PIPA PVC 80cm')
            ->assertDontSee('SEMEN PUTIH');
    }

    public function test_barang_can_be_created(): void
    {
        $this->post('/barang', [
            'kode_barang' => 'NON001',
            'nama_barang' => 'Barang Baru',
            'jenis_barang' => 'barang_jadi',
            'satuan' => 'm2',
            'harga_beli' => 1000,
            'harga_jual' => 1500,
            'stok' => 2,
        ])->assertRedirect('/barang');

        $this->assertDatabaseHas('barangs', [
            'kode_barang' => 'NON001',
            'nama_barang' => 'Barang Baru',
            'jenis_barang' => 'barang_jadi',
            'satuan' => 'm2',
        ]);
    }

    public function test_finished_goods_can_be_created_with_empty_prices_and_stock(): void
    {
        $this->post('/barang', [
            'kode_barang' => 'BJ001',
            'nama_barang' => 'Tembok 1 m2',
            'jenis_barang' => 'barang_jadi',
            'satuan' => 'm2',
            'harga_beli' => '',
            'harga_jual' => '',
            'stok' => '',
        ])->assertRedirect('/barang');

        $this->assertDatabaseHas('barangs', [
            'kode_barang' => 'BJ001',
            'nama_barang' => 'Tembok 1 m2',
            'jenis_barang' => 'barang_jadi',
            'satuan' => 'm2',
            'harga_beli' => 0,
            'harga_jual' => 0,
            'stok' => 0,
        ]);
    }

    public function test_barang_stock_cannot_be_changed_from_edit_barang(): void
    {
        $barang = Barang::create([
            'kode_barang' => 'KAS123',
            'nama_barang' => 'PIPA PVC 80cm',
            'jenis_barang' => 'barang_dagang',
            'satuan' => 'pcs',
            'harga_beli' => 17000,
            'harga_jual' => 20000,
            'stok' => 10,
        ]);

        $this->put("/barang/{$barang->id}", [
            'kode_barang' => 'KAS123',
            'nama_barang' => 'PIPA PVC 80cm',
            'jenis_barang' => 'barang_dagang',
            'satuan' => 'pcs',
            'harga_beli' => 18000,
            'harga_jual' => 21000,
            'stok' => 999,
        ])->assertRedirect('/barang');

        $barang->refresh();

        $this->assertSame('10.000', $barang->stok);
        $this->assertSame('18000.00', $barang->harga_beli);
        $this->assertSame('21000.00', $barang->harga_jual);
    }
}
