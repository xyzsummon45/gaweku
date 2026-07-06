<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarangTest extends TestCase
{
    use RefreshDatabase;

    public function test_barang_list_can_be_searched_by_name_or_code(): void
    {
        $supplier = Supplier::create([
            'nama_supplier' => 'CV Semen Jaya',
        ]);

        Barang::create([
            'supplier_id' => $supplier->id,
            'kode_barang' => 'AAA11',
            'nama_barang' => 'SEMEN PUTIH',
            'harga_beli' => 20000,
            'harga_jual' => 30000,
            'stok' => 24.3,
        ]);

        Barang::create([
            'kode_barang' => 'KAS123',
            'nama_barang' => 'PIPA PVC 80cm',
            'harga_beli' => 17000,
            'harga_jual' => 20000,
            'stok' => 6,
        ]);

        $this->get('/barang?q=semen')
            ->assertOk()
            ->assertSee('SEMEN PUTIH')
            ->assertSee('CV Semen Jaya')
            ->assertSee('24,3')
            ->assertDontSee('PIPA PVC 80cm');

        $this->get('/barang?q=CV Semen')
            ->assertOk()
            ->assertSee('SEMEN PUTIH')
            ->assertDontSee('PIPA PVC 80cm');

        $this->get('/barang?q=KAS123')
            ->assertOk()
            ->assertSee('PIPA PVC 80cm')
            ->assertDontSee('SEMEN PUTIH');
    }

    public function test_barang_can_be_created_without_supplier(): void
    {
        $this->post('/barang', [
            'supplier_id' => '',
            'kode_barang' => 'NON001',
            'nama_barang' => 'Barang Tanpa Supplier',
            'harga_beli' => 1000,
            'harga_jual' => 1500,
            'stok' => 2,
        ])->assertRedirect('/barang');

        $this->assertDatabaseHas('barangs', [
            'kode_barang' => 'NON001',
            'supplier_id' => null,
        ]);
    }
}
