<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\StokMutasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        $keyword = trim($filters['q'] ?? '');

        $opnames = StokMutasi::with('barang')
            ->where('tipe', 'opname')
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('catatan', 'like', "%{$keyword}%")
                        ->orWhereHas('barang', function ($query) use ($keyword) {
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

        return view('stock-opname.index', [
            'opnames' => $opnames,
            'filters' => [
                'q' => $keyword,
                'tanggal_mulai' => $filters['tanggal_mulai'] ?? '',
                'tanggal_selesai' => $filters['tanggal_selesai'] ?? '',
            ],
        ]);
    }

    public function create()
    {
        return view('stock-opname.create');
    }

    public function autocompleteBarang(Request $request)
    {
        $keyword = trim((string) $request->query('q'));

        if (strlen($keyword) < 2) {
            return response()->json([]);
        }

        $barangs = Barang::query()
            ->where(function ($query) use ($keyword) {
                $query->where('nama_barang', 'like', "%{$keyword}%")
                    ->orWhere('kode_barang', 'like', "%{$keyword}%");
            })
            ->orderBy('nama_barang')
            ->limit(10)
            ->get(['id', 'kode_barang', 'nama_barang', 'jenis_barang', 'satuan', 'stok']);

        return response()->json($barangs->map(fn (Barang $barang) => [
            'id' => $barang->id,
            'kode_barang' => $barang->kode_barang,
            'nama_barang' => $barang->nama_barang,
            'jenis_barang' => ucwords(str_replace('_', ' ', $barang->jenis_barang)),
            'satuan' => $barang->satuan,
            'stok' => (float) $barang->stok,
        ]));
    }

    public function store(Request $request)
    {
        $request->merge([
            'stok_fisik' => $this->normalizeNumber($request->input('stok_fisik')),
        ]);

        $data = $request->validate([
            'barang_id' => ['required', 'integer', 'exists:barangs,id'],
            'tanggal' => ['required', 'date'],
            'stok_fisik' => ['required', 'numeric', 'min:0'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($data) {
            $barang = Barang::whereKey($data['barang_id'])->lockForUpdate()->firstOrFail();
            $stokSebelum = (float) $barang->stok;
            $stokSesudah = (float) $data['stok_fisik'];
            $selisih = $stokSesudah - $stokSebelum;

            $barang->update([
                'stok' => $stokSesudah,
            ]);

            StokMutasi::create([
                'barang_id' => $barang->id,
                'tanggal' => $data['tanggal'],
                'tipe' => 'opname',
                'qty_masuk' => $selisih > 0 ? $selisih : 0,
                'qty_keluar' => $selisih < 0 ? abs($selisih) : 0,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'referensi_tipe' => 'stock_opname',
                'catatan' => $data['catatan'] ?? null,
            ]);
        });

        return redirect()
            ->route('stock-opname.index')
            ->with('success', 'Stock opname berhasil disimpan.');
    }

    private function normalizeNumber(mixed $value): string
    {
        return str_replace(',', '.', trim((string) $value));
    }
}
