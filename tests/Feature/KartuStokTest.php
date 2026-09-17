<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\StokMutasi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KartuStokTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_card_shows_mutations_for_selected_item_and_date_range(): void
    {
        $barang = Barang::create([
            'kode_barang' => 'AAA11',
            'nama_barang' => 'SEMEN PUTIH',
            'jenis_barang' => 'bahan_baku',
            'satuan' => 'kg',
            'harga_beli' => 20000,
            'harga_jual' => 30000,
            'stok' => 12,
        ]);

        StokMutasi::create([
            'barang_id' => $barang->id,
            'tanggal' => '2026-09-09 08:00:00',
            'tipe' => 'pembelian',
            'qty_masuk' => 10,
            'qty_keluar' => 0,
            'stok_sebelum' => 0,
            'stok_sesudah' => 10,
            'referensi_tipe' => 'pembelian',
            'referensi_id' => 1,
            'catatan' => 'Sebelum periode',
        ]);

        StokMutasi::create([
            'barang_id' => $barang->id,
            'tanggal' => '2026-09-10 09:00:00',
            'tipe' => 'penjualan',
            'qty_masuk' => 0,
            'qty_keluar' => 2,
            'stok_sebelum' => 10,
            'stok_sesudah' => 8,
            'referensi_tipe' => 'transaksi',
            'referensi_id' => 2,
            'catatan' => 'Penjualan TRX-001',
        ]);

        StokMutasi::create([
            'barang_id' => $barang->id,
            'tanggal' => '2026-09-11 10:00:00',
            'tipe' => 'produksi_bahan',
            'qty_masuk' => 0,
            'qty_keluar' => 3,
            'stok_sebelum' => 8,
            'stok_sesudah' => 5,
            'referensi_tipe' => 'produksi',
            'referensi_id' => 3,
            'catatan' => 'Bahan dipakai produksi PRD-001',
        ]);

        $this->get("/kartu-stok?barang_id={$barang->id}&tanggal_mulai=2026-09-10&tanggal_selesai=2026-09-11")
            ->assertOk()
            ->assertSee('AAA11 - SEMEN PUTIH')
            ->assertSee('Stok Awal Periode')
            ->assertSee('10 kg')
            ->assertSee('Stok Akhir Periode')
            ->assertSee('5 kg')
            ->assertSee('Penjualan TRX-001')
            ->assertSee('Bahan dipakai produksi PRD-001')
            ->assertDontSee('Sebelum periode');
    }

    public function test_stock_card_barang_autocomplete_searches_by_keyword(): void
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

        $this->getJson('/kartu-stok/autocomplete-barang?q=semen')
            ->assertOk()
            ->assertJsonFragment([
                'id' => $barang->id,
                'kode_barang' => 'AAA11',
                'nama_barang' => 'SEMEN PUTIH',
                'satuan' => 'kg',
                'stok' => 8,
            ]);
    }
}
