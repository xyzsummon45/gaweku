<?php

namespace Tests\Feature;

use App\Models\KasAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KasTest extends TestCase
{
    use RefreshDatabase;

    public function test_kas_can_record_income_expense_and_transfer(): void
    {
        $kasBank = KasAccount::where('kode', KasAccount::KAS_BANK)->first();
        $kasBesar = KasAccount::where('kode', KasAccount::KAS_BESAR)->first();

        $this->post('/kas', [
            'jenis' => 'pemasukan',
            'kas_account_id' => $kasBank->id,
            'tanggal' => '2026-07-06 10:00:00',
            'jumlah' => '100000',
            'keterangan' => 'Saldo awal',
        ])->assertRedirect('/kas');

        $this->post('/kas', [
            'jenis' => 'mutasi',
            'kas_asal_id' => $kasBank->id,
            'kas_tujuan_id' => $kasBesar->id,
            'tanggal' => '2026-07-06 11:00:00',
            'jumlah' => '40000',
            'keterangan' => 'Pindah ke kas besar',
        ])->assertRedirect('/kas');

        $this->post('/kas', [
            'jenis' => 'pengeluaran',
            'kas_account_id' => $kasBesar->id,
            'tanggal' => '2026-07-06 12:00:00',
            'jumlah' => '15000',
            'keterangan' => 'Operasional',
        ])->assertRedirect('/kas');

        $this->assertSame('60000.00', $kasBank->fresh()->saldo);
        $this->assertSame('25000.00', $kasBesar->fresh()->saldo);
        $this->assertDatabaseHas('kas_mutations', [
            'kas_account_id' => $kasBank->id,
            'jenis' => 'mutasi_keluar',
            'jumlah' => 40000,
        ]);
        $this->assertDatabaseHas('kas_mutations', [
            'kas_account_id' => $kasBesar->id,
            'jenis' => 'mutasi_masuk',
            'jumlah' => 40000,
        ]);
    }
}
