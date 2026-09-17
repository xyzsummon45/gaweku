<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\StokMutasi;
use Illuminate\Http\Request;

class KartuStokController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'barang_id' => ['nullable', 'integer', 'exists:barangs,id'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        $tanggalMulai = $filters['tanggal_mulai'] ?? now()->toDateString();
        $tanggalSelesai = $filters['tanggal_selesai'] ?? $tanggalMulai;
        $barang = null;
        $mutasis = collect();
        $stokAwal = null;
        $stokAkhir = null;

        if (! empty($filters['barang_id'])) {
            $barang = Barang::find($filters['barang_id']);

            $mutasis = StokMutasi::where('barang_id', $barang->id)
                ->whereDate('tanggal', '>=', $tanggalMulai)
                ->whereDate('tanggal', '<=', $tanggalSelesai)
                ->orderBy('tanggal')
                ->orderBy('id')
                ->get();

            $mutasiSebelum = StokMutasi::where('barang_id', $barang->id)
                ->whereDate('tanggal', '<', $tanggalMulai)
                ->latest('tanggal')
                ->latest('id')
                ->first();

            $stokAwal = $mutasis->first()?->stok_sebelum
                ?? $mutasiSebelum?->stok_sesudah
                ?? $barang->stok;
            $stokAkhir = $mutasis->last()?->stok_sesudah ?? $stokAwal;
        }

        return view('kartu-stok.index', [
            'barang' => $barang,
            'mutasis' => $mutasis,
            'stokAwal' => $stokAwal,
            'stokAkhir' => $stokAkhir,
            'filters' => [
                'barang_id' => $filters['barang_id'] ?? '',
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
            ],
        ]);
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
}
