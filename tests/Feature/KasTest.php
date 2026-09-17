<?php

namespace Tests\Feature;

use App\Models\KasAccount;
use App\Models\KasMutation;
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

    public function test_kas_mutation_history_is_paginated_by_ten_rows(): void
    {
        $kasBank = KasAccount::where('kode', KasAccount::KAS_BANK)->first();
        $labels = [
            'Satu Lama',
            'Dua',
            'Tiga',
            'Empat',
            'Lima',
            'Enam',
            'Tujuh',
            'Delapan',
            'Sembilan',
            'Sepuluh',
            'Sebelas Baru',
        ];

        foreach (range(1, 11) as $index) {
            KasMutation::create([
                'kas_account_id' => $kasBank->id,
                'tanggal' => now()->subMinutes(11 - $index),
                'jenis' => 'pemasukan',
                'jumlah' => 1000,
                'keterangan' => $labels[$index - 1],
            ]);
        }

        $this->get('/kas')
            ->assertOk()
            ->assertSee('Menampilkan 1-10')
            ->assertSee('Sebelas Baru')
            ->assertSee('Dua')
            ->assertDontSee('Satu Lama');

        $this->get('/kas?page=2')
            ->assertOk()
            ->assertSee('Satu Lama')
            ->assertDontSee('Sebelas Baru');
    }
}
