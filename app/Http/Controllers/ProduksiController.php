<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Formula;
use App\Models\Produksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProduksiController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        $keyword = trim($filters['q'] ?? '');

        $produksis = Produksi::with('barangHasil')
            ->withCount('items')
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('kode_produksi', 'like', "%{$keyword}%")
                        ->orWhere('nama_formula', 'like', "%{$keyword}%")
                        ->orWhereHas('barangHasil', function ($query) use ($keyword) {
                            $query->where('kode_barang', 'like', "%{$keyword}%")
                                ->orWhere('nama_barang', 'like', "%{$keyword}%");
                        });
                });
            })
            ->when($filters['tanggal_mulai'] ?? null, function ($query, $tanggalMulai) {
                $query->whereDate('tanggal', '>=', $tanggalMulai);
            })
            ->when($filters['tanggal_selesai'] ?? null, function ($query, $tanggalSelesai) {
                $query->whereDate('tanggal', '<=', $tanggalSelesai);
            })
            ->latest('tanggal')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('produksi.index', [
            'produksis' => $produksis,
            'filters' => [
                'q' => $keyword,
                'tanggal_mulai' => $filters['tanggal_mulai'] ?? '',
                'tanggal_selesai' => $filters['tanggal_selesai'] ?? '',
            ],
        ]);
    }

    public function create()
    {
        $formulas = Formula::with('items.barangBahan')->where('aktif', true)->orderBy('nama_formula')->get();

        return view('produksi.create', [
            'formulas' => $formulas,
            'formulaOptions' => $formulas->map(fn (Formula $formula) => [
                'id' => $formula->id,
                'qty_hasil' => (float) $formula->qty_hasil,
                'satuan_hasil' => $formula->satuan_hasil,
                'items' => $formula->items->map(fn ($item) => [
                    'kode_barang' => $item->kode_barang,
                    'nama_barang' => $item->nama_barang,
                    'satuan' => $item->satuan,
                    'qty' => (float) $item->qty,
                    'harga_beli' => (float) $item->harga_beli,
                    'stok' => (float) ($item->barangBahan?->stok ?? 0),
                ])->values(),
            ])->values(),
            'barangHasils' => Barang::where('jenis_barang', 'barang_jadi')->orderBy('nama_barang')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->merge([
            'qty_produksi' => $this->normalizeNumber($request->input('qty_produksi')),
        ]);

        $data = $request->validate([
            'formula_id' => ['required', 'integer', 'exists:formulas,id'],
            'barang_hasil_id' => [
                'required',
                'integer',
                Rule::exists('barangs', 'id')->where('jenis_barang', 'barang_jadi'),
            ],
            'tanggal' => ['required', 'date'],
            'qty_produksi' => ['required', 'numeric', 'min:0.001'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $produksi = DB::transaction(function () use ($data) {
            $formula = Formula::with('items')->whereKey($data['formula_id'])->lockForUpdate()->firstOrFail();

            if (! $formula->aktif) {
                throw ValidationException::withMessages([
                    'formula_id' => 'Formula ini sedang nonaktif.',
                ]);
            }

            $ratio = (float) $data['qty_produksi'] / (float) $formula->qty_hasil;
            $bahanIds = $formula->items->pluck('barang_bahan_id')->all();

            if (in_array((int) $data['barang_hasil_id'], array_map('intval', $bahanIds), true)) {
                throw ValidationException::withMessages([
                    'barang_hasil_id' => 'Barang hasil tidak boleh sama dengan bahan yang dipakai.',
                ]);
            }

            $barangs = Barang::whereIn('id', array_merge($bahanIds, [(int) $data['barang_hasil_id']]))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $kurang = [];

            foreach ($formula->items as $item) {
                $barang = $barangs->get($item->barang_bahan_id);
                $qtyPakai = (float) $item->qty * $ratio;

                if (! $barang || (float) $barang->stok < $qtyPakai) {
                    $stok = $barang ? (float) $barang->stok : 0;
                    $kurang[] = "{$item->nama_barang} kurang ".$this->formatQty($qtyPakai - $stok)." {$item->satuan}";
                }
            }

            if ($kurang !== []) {
                throw ValidationException::withMessages([
                    'formula_id' => 'Stok bahan tidak cukup: '.implode(', ', $kurang).'. Beli bahan dulu lewat menu Pembelian.',
                ]);
            }

            $produksi = Produksi::create([
                'formula_id' => $formula->id,
                'barang_hasil_id' => $data['barang_hasil_id'],
                'kode_produksi' => $this->nextProductionCode(),
                'tanggal' => $data['tanggal'],
                'nama_formula' => $formula->nama_formula,
                'qty_formula_hasil' => $formula->qty_hasil,
                'satuan_hasil' => $formula->satuan_hasil,
                'qty_produksi' => $data['qty_produksi'],
                'total_biaya' => 0,
                'hpp' => 0,
                'catatan' => $data['catatan'] ?? null,
            ]);

            $totalBiaya = 0;

            foreach ($formula->items as $item) {
                $barang = $barangs->get($item->barang_bahan_id);
                $qtyPakai = (float) $item->qty * $ratio;
                $hargaBeli = (float) $barang->harga_beli;
                $subtotal = $qtyPakai * $hargaBeli;

                $produksi->items()->create([
                    'barang_bahan_id' => $barang->id,
                    'kode_barang' => $barang->kode_barang,
                    'nama_barang' => $barang->nama_barang,
                    'satuan' => $barang->satuan,
                    'qty_formula' => $item->qty,
                    'qty_pakai' => $qtyPakai,
                    'harga_beli' => $hargaBeli,
                    'subtotal' => $subtotal,
                ]);

                $barang->decrement('stok', $qtyPakai);
                $totalBiaya += $subtotal;
            }

            $hpp = $totalBiaya / (float) $data['qty_produksi'];

            $barangHasil = $barangs->get((int) $data['barang_hasil_id']);
            $barangHasil->update([
                'jenis_barang' => 'barang_jadi',
                'satuan' => $formula->satuan_hasil,
                'harga_beli' => $hpp,
            ]);
            $barangHasil->increment('stok', (float) $data['qty_produksi']);

            $produksi->update([
                'total_biaya' => $totalBiaya,
                'hpp' => $hpp,
            ]);

            return $produksi;
        });

        return redirect()
            ->route('produksi.show', $produksi)
            ->with('success', 'Produksi berhasil disimpan. Stok bahan berkurang dan stok hasil bertambah.');
    }

    public function show(Produksi $produksi)
    {
        $produksi->load(['barangHasil', 'items']);

        return view('produksi.show', compact('produksi'));
    }

    private function nextProductionCode(): string
    {
        $prefix = 'PRD-'.now()->format('Ymd').'-';
        $count = Produksi::where('kode_produksi', 'like', "{$prefix}%")->count() + 1;

        return $prefix.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }

    private function normalizeNumber(mixed $value): string
    {
        return str_replace(',', '.', trim((string) $value));
    }

    private function formatQty(float $value): string
    {
        return rtrim(rtrim(number_format(max(0, $value), 3, ',', '.'), '0'), ',');
    }
}
