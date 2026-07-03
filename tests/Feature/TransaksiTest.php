<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransaksiTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_search_barang_and_store_transaction(): void
    {
        $barang = Barang::create([
            'kode_barang' => 'KAS123',
            'nama_barang' => 'PIPA PVC 80cm',
            'harga_beli' => 17000,
            'harga_jual' => 20000,
            'stok' => 10,
        ]);

        $this->getJson('/transaksi/autocomplete?q=pipa')
            ->assertOk()
            ->assertJsonFragment([
                'id' => $barang->id,
                'kode_barang' => 'KAS123',
                'nama_barang' => 'PIPA PVC 80cm',
                'harga_jual' => 20000,
                'stok' => 10,
            ]);

        $response = $this->post('/transaksi', [
            'barang_id' => [$barang->id],
            'qty' => [3],
        ]);

        $transaksi = Transaksi::first();

        $response->assertRedirect(route('transaksi.show', $transaksi));
        $this->assertDatabaseHas('transaksis', [
            'id' => $transaksi->id,
            'total' => 60000,
        ]);
        $this->assertDatabaseHas('transaksi_items', [
            'transaksi_id' => $transaksi->id,
            'barang_id' => $barang->id,
            'qty' => 3,
            'subtotal' => 60000,
        ]);
        $this->assertSame(7, $barang->fresh()->stok);
    }
}
