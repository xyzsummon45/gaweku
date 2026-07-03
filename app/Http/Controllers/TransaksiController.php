<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksis = Transaksi::withCount('items')
            ->latest('tanggal')
            ->get();

        return view('transaksi.index', compact('transaksis'));
    }

    public function create()
    {
        return view('transaksi.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'barang_id' => ['required', 'array', 'min:1'],
            'barang_id.*' => ['required', 'integer', 'exists:barangs,id'],
            'qty' => ['required', 'array', 'min:1'],
            'qty.*' => ['required', 'integer', 'min:1'],
        ]);

        $groupedItems = [];

        foreach ($data['barang_id'] as $index => $barangId) {
            $groupedItems[$barangId] = ($groupedItems[$barangId] ?? 0) + (int) $data['qty'][$index];
        }

        $transaksi = DB::transaction(function () use ($groupedItems) {
            $barangs = Barang::whereIn('id', array_keys($groupedItems))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($groupedItems as $barangId => $qty) {
                $barang = $barangs->get($barangId);

                if (! $barang || $barang->stok < $qty) {
                    throw ValidationException::withMessages([
                        'barang_id' => 'Stok barang tidak cukup untuk transaksi ini.',
                    ]);
                }
            }

            $transaksi = Transaksi::create([
                'kode_transaksi' => $this->nextTransactionCode(),
                'tanggal' => now(),
                'total' => 0,
            ]);

            $total = 0;

            foreach ($groupedItems as $barangId => $qty) {
                $barang = $barangs->get($barangId);
                $subtotal = $barang->harga_jual * $qty;

                $transaksi->items()->create([
                    'barang_id' => $barang->id,
                    'kode_barang' => $barang->kode_barang,
                    'nama_barang' => $barang->nama_barang,
                    'harga_jual' => $barang->harga_jual,
                    'qty' => $qty,
                    'subtotal' => $subtotal,
                ]);

                $barang->decrement('stok', $qty);
                $total += $subtotal;
            }

            $transaksi->update(['total' => $total]);

            return $transaksi;
        });

        return redirect()
            ->route('transaksi.show', $transaksi)
            ->with('success', 'Transaksi berhasil disimpan.');
    }

    public function show(Transaksi $transaksi)
    {
        $transaksi->load('items');

        return view('transaksi.show', compact('transaksi'));
    }

    public function autocomplete(Request $request)
    {
        $keyword = trim((string) $request->query('q'));

        if (strlen($keyword) < 2) {
            return response()->json([]);
        }

        $barangs = Barang::query()
            ->where('stok', '>', 0)
            ->where(function ($query) use ($keyword) {
                $query->where('nama_barang', 'like', "%{$keyword}%")
                    ->orWhere('kode_barang', 'like', "%{$keyword}%");
            })
            ->orderBy('nama_barang')
            ->limit(10)
            ->get(['id', 'kode_barang', 'nama_barang', 'harga_jual', 'stok']);

        return response()->json($barangs->map(fn (Barang $barang) => [
            'id' => $barang->id,
            'kode_barang' => $barang->kode_barang,
            'nama_barang' => $barang->nama_barang,
            'harga_jual' => (float) $barang->harga_jual,
            'stok' => $barang->stok,
        ]));
    }

    private function nextTransactionCode(): string
    {
        $prefix = 'TRX-'.now()->format('Ymd').'-';
        $count = Transaksi::where('kode_transaksi', 'like', "{$prefix}%")->count() + 1;

        return $prefix.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
