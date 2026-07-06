<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\KasAccount;
use App\Models\Pembelian;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembelianTest extends TestCase
{
    use RefreshDatabase;

    public function test_supplier_invoice_can_add_stock_and_record_debt(): void
    {
        $supplier = Supplier::create([
            'nama_supplier' => 'CV Semen Jaya',
        ]);

        $barang = Barang::create([
            'kode_barang' => 'AAA11',
            'nama_barang' => 'SEMEN PUTIH',
            'harga_beli' => 1000,
            'harga_jual' => 1500,
            'stok' => 10,
        ]);

        $response = $this->post('/pembelian', [
            'supplier_id' => $supplier->id,
            'nomor_invoice' => 'INV-001',
            'tanggal' => '2026-07-06',
            'tanggal_jatuh_tempo' => '2026-07-20',
            'barang_id' => [$barang->id],
            'qty' => ['2,5'],
            'harga_beli' => ['2000'],
        ]);

        $pembelian = Pembelian::first();

        $response->assertRedirect(route('pembelian.show', $pembelian));
        $this->assertDatabaseHas('pembelians', [
            'id' => $pembelian->id,
            'supplier_id' => $supplier->id,
            'nomor_invoice' => 'INV-001',
            'total' => 5000,
            'status_pembayaran' => 'belum_lunas',
        ]);
        $this->assertDatabaseHas('pembelian_items', [
            'pembelian_id' => $pembelian->id,
            'barang_id' => $barang->id,
            'qty' => 2.5,
            'harga_beli' => 2000,
            'subtotal' => 5000,
        ]);
        $this->assertSame('12.500', $barang->fresh()->stok);
        $this->assertSame('2000.00', $barang->fresh()->harga_beli);
    }

    public function test_supplier_invoice_can_be_paid_from_kas(): void
    {
        $supplier = Supplier::create([
            'nama_supplier' => 'CV Semen Jaya',
        ]);

        $kasBank = KasAccount::where('kode', KasAccount::KAS_BANK)->first();
        $kasBank->update(['saldo' => 10000]);

        $pembelian = Pembelian::create([
            'supplier_id' => $supplier->id,
            'kode_pembelian' => 'PB-20260706-0001',
            'nomor_invoice' => 'INV-001',
            'tanggal' => '2026-07-06',
            'tanggal_jatuh_tempo' => '2026-07-20',
            'total' => 5000,
            'jumlah_dibayar' => 0,
            'status_pembayaran' => 'belum_lunas',
        ]);

        $this->post(route('pembelian.bayar', $pembelian), [
            'kas_account_id' => $kasBank->id,
            'tanggal' => '2026-07-07',
            'jumlah' => '5000',
            'catatan' => 'Transfer invoice',
        ])->assertRedirect(route('pembelian.show', $pembelian));

        $this->assertSame('5000.00', $kasBank->fresh()->saldo);
        $this->assertDatabaseHas('pembelians', [
            'id' => $pembelian->id,
            'jumlah_dibayar' => 5000,
            'status_pembayaran' => 'lunas',
        ]);
        $this->assertDatabaseHas('kas_mutations', [
            'pembelian_id' => $pembelian->id,
            'kas_account_id' => $kasBank->id,
            'jenis' => 'pembayaran_hutang',
            'jumlah' => 5000,
        ]);
    }

    public function test_purchase_barang_autocomplete_returns_stock_and_buy_price(): void
    {
        $barang = Barang::create([
            'kode_barang' => 'AAA11',
            'nama_barang' => 'SEMEN PUTIH',
            'harga_beli' => 20000,
            'harga_jual' => 30000,
            'stok' => 24.3,
        ]);

        $this->getJson('/pembelian/autocomplete-barang?q=semen')
            ->assertOk()
            ->assertJsonFragment([
                'id' => $barang->id,
                'kode_barang' => 'AAA11',
                'nama_barang' => 'SEMEN PUTIH',
                'harga_beli' => 20000,
                'stok' => 24.3,
            ]);
    }
}
