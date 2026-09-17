<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\KasAccount;
use App\Models\KasMutation;
use App\Models\Transaksi;
use Carbon\Carbon;
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
            'qty' => ['0,5'],
        ]);

        $transaksi = Transaksi::first();

        $response->assertRedirect(route('transaksi.show', $transaksi));
        $this->assertDatabaseHas('transaksis', [
            'id' => $transaksi->id,
            'total' => 10000,
        ]);
        $this->assertDatabaseHas('transaksi_items', [
            'transaksi_id' => $transaksi->id,
            'barang_id' => $barang->id,
            'qty' => 0.5,
            'harga_modal' => 17000,
            'subtotal' => 10000,
            'subtotal_modal' => 8500,
            'laba_kotor' => 1500,
        ]);
        $this->assertSame('9.500', $barang->fresh()->stok);
        $this->assertSame('10000.00', KasAccount::where('kode', KasAccount::KAS_BANK)->first()->saldo);
        $this->assertDatabaseHas('kas_mutations', [
            'transaksi_id' => $transaksi->id,
            'jenis' => 'pemasukan',
            'jumlah' => 10000,
        ]);
        $this->assertDatabaseHas('stok_mutasis', [
            'barang_id' => $barang->id,
            'tipe' => 'penjualan',
            'qty_masuk' => 0,
            'qty_keluar' => 0.5,
            'stok_sebelum' => 10,
            'stok_sesudah' => 9.5,
            'referensi_tipe' => 'transaksi',
            'referensi_id' => $transaksi->id,
        ]);
    }

    public function test_transaction_history_can_be_filtered_by_date(): void
    {
        Transaksi::create([
            'kode_transaksi' => 'TRX-20260702-0001',
            'tanggal' => Carbon::parse('2026-07-02 10:00:00'),
            'total' => 10000,
        ]);

        Transaksi::create([
            'kode_transaksi' => 'TRX-20260703-0001',
            'tanggal' => Carbon::parse('2026-07-03 10:00:00'),
            'total' => 20000,
        ]);

        $this->get('/transaksi?tanggal_mulai=2026-07-03&tanggal_selesai=2026-07-03')
            ->assertOk()
            ->assertSee('TRX-20260703-0001')
            ->assertDontSee('TRX-20260702-0001');
    }
}
