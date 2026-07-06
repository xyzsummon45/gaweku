<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pembelian;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'status_pembayaran' => ['nullable', 'in:belum_lunas,sebagian,lunas'],
        ]);

        $keyword = trim($filters['q'] ?? '');

        $pembelians = Pembelian::with('supplier')
            ->withCount('items')
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('kode_pembelian', 'like', "%{$keyword}%")
                        ->orWhere('nomor_invoice', 'like', "%{$keyword}%")
                        ->orWhereHas('supplier', function ($query) use ($keyword) {
                            $query->where('nama_supplier', 'like', "%{$keyword}%");
                        });
                });
            })
            ->when($filters['tanggal_mulai'] ?? null, function ($query, $tanggalMulai) {
                $query->whereDate('tanggal', '>=', $tanggalMulai);
            })
            ->when($filters['tanggal_selesai'] ?? null, function ($query, $tanggalSelesai) {
                $query->whereDate('tanggal', '<=', $tanggalSelesai);
            })
            ->when($filters['status_pembayaran'] ?? null, function ($query, $status) {
                $query->where('status_pembayaran', $status);
            })
            ->latest('tanggal')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('pembelian.index', [
            'pembelians' => $pembelians,
            'filters' => [
                'q' => $keyword,
                'tanggal_mulai' => $filters['tanggal_mulai'] ?? '',
                'tanggal_selesai' => $filters['tanggal_selesai'] ?? '',
                'status_pembayaran' => $filters['status_pembayaran'] ?? '',
            ],
        ]);
    }

    public function create()
    {
        return view('pembelian.create', [
            'suppliers' => Supplier::orderBy('nama_supplier')->get(['id', 'nama_supplier']),
        ]);
    }

    public function store(Request $request)
    {
        $request->merge([
            'qty' => array_map(
                fn ($qty) => $this->normalizeNumber($qty),
                $request->input('qty', [])
            ),
            'harga_beli' => array_map(
                fn ($harga) => $this->normalizeNumber($harga),
                $request->input('harga_beli', [])
            ),
        ]);

        $data = $request->validate([
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'nomor_invoice' => ['required', 'string', 'max:100', 'unique:pembelians,nomor_invoice'],
            'tanggal' => ['required', 'date'],
            'tanggal_jatuh_tempo' => ['required', 'date', 'after_or_equal:tanggal'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'barang_id' => ['required', 'array', 'min:1'],
            'barang_id.*' => ['required', 'integer', 'exists:barangs,id'],
            'qty' => ['required', 'array', 'min:1'],
            'qty.*' => ['required', 'numeric', 'min:0.001'],
            'harga_beli' => ['required', 'array', 'min:1'],
            'harga_beli.*' => ['required', 'numeric', 'min:0'],
        ]);

        $pembelian = DB::transaction(function () use ($data) {
            $barangs = Barang::whereIn('id', $data['barang_id'])
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $pembelian = Pembelian::create([
                'supplier_id' => $data['supplier_id'],
                'kode_pembelian' => $this->nextPurchaseCode(),
                'nomor_invoice' => $data['nomor_invoice'],
                'tanggal' => $data['tanggal'],
                'tanggal_jatuh_tempo' => $data['tanggal_jatuh_tempo'],
                'total' => 0,
                'status_pembayaran' => 'belum_lunas',
                'catatan' => $data['catatan'] ?? null,
            ]);

            $items = [];

            foreach ($data['barang_id'] as $index => $barangId) {
                $items[$barangId] ??= [
                    'qty' => 0,
                    'harga_beli' => (float) $data['harga_beli'][$index],
                ];
                $items[$barangId]['qty'] += (float) $data['qty'][$index];
                $items[$barangId]['harga_beli'] = (float) $data['harga_beli'][$index];
            }

            $total = 0;

            foreach ($items as $barangId => $item) {
                $barang = $barangs->get($barangId);
                $qty = $item['qty'];
                $hargaBeli = $item['harga_beli'];
                $subtotal = $qty * $hargaBeli;

                $pembelian->items()->create([
                    'barang_id' => $barang->id,
                    'kode_barang' => $barang->kode_barang,
                    'nama_barang' => $barang->nama_barang,
                    'qty' => $qty,
                    'harga_beli' => $hargaBeli,
                    'subtotal' => $subtotal,
                ]);

                $barang->increment('stok', $qty);
                $barang->update(['harga_beli' => $hargaBeli]);
                $total += $subtotal;
            }

            $pembelian->update(['total' => $total]);

            return $pembelian;
        });

        return redirect()
            ->route('pembelian.show', $pembelian)
            ->with('success', 'Pembelian berhasil disimpan dan stok barang sudah bertambah.');
    }

    public function show(Pembelian $pembelian)
    {
        $pembelian->load(['supplier', 'items']);

        return view('pembelian.show', compact('pembelian'));
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
            ->get(['id', 'kode_barang', 'nama_barang', 'harga_beli', 'stok']);

        return response()->json($barangs->map(fn (Barang $barang) => [
            'id' => $barang->id,
            'kode_barang' => $barang->kode_barang,
            'nama_barang' => $barang->nama_barang,
            'harga_beli' => (float) $barang->harga_beli,
            'stok' => (float) $barang->stok,
        ]));
    }

    private function nextPurchaseCode(): string
    {
        $prefix = 'PB-'.now()->format('Ymd').'-';
        $count = Pembelian::where('kode_pembelian', 'like', "{$prefix}%")->count() + 1;

        return $prefix.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }

    private function normalizeNumber(mixed $value): string
    {
        return str_replace(',', '.', trim((string) $value));
    }
}
