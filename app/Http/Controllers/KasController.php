<?php

namespace App\Http\Controllers;

use App\Models\KasAccount;
use App\Models\KasMutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KasController extends Controller
{
    public function index()
    {
        return view('kas.index', [
            'kasAccounts' => KasAccount::orderByRaw("CASE kode WHEN 'kas_bank' THEN 1 WHEN 'kas_besar' THEN 2 WHEN 'kas_kecil' THEN 3 ELSE 4 END")->get(),
            'mutations' => KasMutation::with(['kasAccount', 'relatedKasAccount', 'pembelian', 'transaksi'])
                ->latest('tanggal')
                ->latest('id')
                ->paginate(15),
        ]);
    }

    public function store(Request $request)
    {
        $request->merge([
            'jumlah' => $this->normalizeNumber($request->input('jumlah')),
        ]);

        $data = $request->validate([
            'jenis' => ['required', 'in:pemasukan,pengeluaran,mutasi'],
            'kas_account_id' => ['required_unless:jenis,mutasi', 'nullable', 'integer', 'exists:kas_accounts,id'],
            'kas_asal_id' => ['required_if:jenis,mutasi', 'nullable', 'integer', 'exists:kas_accounts,id'],
            'kas_tujuan_id' => ['required_if:jenis,mutasi', 'nullable', 'integer', 'exists:kas_accounts,id', 'different:kas_asal_id'],
            'tanggal' => ['required', 'date'],
            'jumlah' => ['required', 'numeric', 'min:0.01'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($data) {
            if ($data['jenis'] === 'mutasi') {
                $this->transfer($data);

                return;
            }

            $kas = KasAccount::whereKey($data['kas_account_id'])->lockForUpdate()->firstOrFail();
            $jumlah = (float) $data['jumlah'];

            if ($data['jenis'] === 'pengeluaran' && (float) $kas->saldo < $jumlah) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Saldo kas tidak cukup untuk pengeluaran ini.',
                ]);
            }

            $data['jenis'] === 'pemasukan'
                ? $kas->increment('saldo', $jumlah)
                : $kas->decrement('saldo', $jumlah);

            KasMutation::create([
                'kas_account_id' => $kas->id,
                'tanggal' => $data['tanggal'],
                'jenis' => $data['jenis'],
                'jumlah' => $jumlah,
                'keterangan' => $data['keterangan'] ?? null,
            ]);
        });

        return redirect()
            ->route('kas.index')
            ->with('success', 'Mutasi kas berhasil disimpan.');
    }

    private function transfer(array $data): void
    {
        $accounts = KasAccount::whereIn('id', [$data['kas_asal_id'], $data['kas_tujuan_id']])
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        $kasAsal = $accounts->get($data['kas_asal_id']);
        $kasTujuan = $accounts->get($data['kas_tujuan_id']);
        $jumlah = (float) $data['jumlah'];

        if (! $kasAsal || ! $kasTujuan) {
            throw ValidationException::withMessages([
                'kas_asal_id' => 'Akun kas tidak valid.',
            ]);
        }

        if ((float) $kasAsal->saldo < $jumlah) {
            throw ValidationException::withMessages([
                'jumlah' => 'Saldo kas asal tidak cukup untuk mutasi ini.',
            ]);
        }

        $kasAsal->decrement('saldo', $jumlah);
        $kasTujuan->increment('saldo', $jumlah);

        KasMutation::create([
            'kas_account_id' => $kasAsal->id,
            'related_kas_account_id' => $kasTujuan->id,
            'tanggal' => $data['tanggal'],
            'jenis' => 'mutasi_keluar',
            'jumlah' => $jumlah,
            'keterangan' => $data['keterangan'] ?? null,
        ]);

        KasMutation::create([
            'kas_account_id' => $kasTujuan->id,
            'related_kas_account_id' => $kasAsal->id,
            'tanggal' => $data['tanggal'],
            'jenis' => 'mutasi_masuk',
            'jumlah' => $jumlah,
            'keterangan' => $data['keterangan'] ?? null,
        ]);
    }

    private function normalizeNumber(mixed $value): string
    {
        return str_replace(',', '.', trim((string) $value));
    }
}
