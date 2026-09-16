<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanPenjualanTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_profit_report_shows_summary_and_items(): void
    {
        $barang = Barang::create([
            'kode_barang' => 'AAA11',
            'nama_barang' => 'SEMEN PUTIH',
            'jenis_barang' => 'bahan_baku',
            'satuan' => 'kg',
            'harga_beli' => 20000,
            'harga_jual' => 30000,
            'stok' => 10,
        ]);

        $transaksi = Transaksi::create([
            'kode_transaksi' => 'TRX-20260916-0001',
            'tanggal' => '2026-09-16 10:00:00',
            'total' => 60000,
        ]);
        $transaksi->items()->create([
            'barang_id' => $barang->id,
            'kode_barang' => $barang->kode_barang,
            'nama_barang' => $barang->nama_barang,
            'harga_jual' => 30000,
            'harga_modal' => 20000,
            'qty' => 2,
            'subtotal' => 60000,
            'subtotal_modal' => 40000,
            'laba_kotor' => 20000,
        ]);

        $this->get('/laporan/penjualan?tanggal_mulai=2026-09-16&tanggal_selesai=2026-09-16')
            ->assertOk()
            ->assertSee('Laba Rugi Jual')
            ->assertSee('TRX-20260916-0001')
            ->assertSee('SEMEN PUTIH')
            ->assertSee('Rp 60.000')
            ->assertSee('Rp 40.000')
            ->assertSee('Rp 20.000');
    }

    public function test_sales_profit_report_can_be_downloaded_as_pdf(): void
    {
        $response = $this->get('/laporan/penjualan/pdf?tanggal_mulai=2026-09-16&tanggal_selesai=2026-09-16');

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
    }
}
