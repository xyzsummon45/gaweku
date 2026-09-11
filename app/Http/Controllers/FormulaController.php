<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Formula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FormulaController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $keyword = trim($filters['q'] ?? '');

        $formulas = Formula::with('barangJadi')
            ->withCount('items')
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('nama_formula', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('nama_formula')
            ->paginate(10)
            ->withQueryString();

        return view('formula.index', [
            'formulas' => $formulas,
            'filters' => ['q' => $keyword],
        ]);
    }

    public function create()
    {
        return view('formula.create', $this->formData(new Formula()));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $formula = DB::transaction(function () use ($data) {
            $formula = Formula::create($this->formulaPayload($data));
            $this->syncItems($formula, $data);

            return $formula;
        });

        return redirect()
            ->route('formula.show', $formula)
            ->with('success', 'Formula berhasil disimpan.');
    }

    public function show(Formula $formula)
    {
        $formula->load(['barangJadi', 'items']);

        return view('formula.show', compact('formula'));
    }

    public function edit(Formula $formula)
    {
        $formula->load('items');

        return view('formula.edit', $this->formData($formula));
    }

    public function update(Request $request, Formula $formula)
    {
        $data = $this->validatedData($request, $formula);

        DB::transaction(function () use ($data, $formula) {
            $formula->update($this->formulaPayload($data));
            $formula->items()->delete();
            $this->syncItems($formula, $data);
        });

        return redirect()
            ->route('formula.show', $formula)
            ->with('success', 'Formula berhasil diperbarui.');
    }

    public function destroy(Formula $formula)
    {
        $formula->delete();

        return redirect()
            ->route('formula.index')
            ->with('success', 'Formula berhasil dihapus.');
    }

    private function formData(Formula $formula): array
    {
        return [
            'formula' => $formula,
            'barangBahans' => Barang::whereIn('jenis_barang', ['bahan_baku', 'bahan_penolong', 'barang_dagang'])->orderBy('nama_barang')->get(),
        ];
    }

    private function validatedData(Request $request, ?Formula $formula = null): array
    {
        $request->merge([
            'qty_hasil' => $this->normalizeNumber($request->input('qty_hasil')),
            'satuan_hasil' => $this->normalizeSatuan($request->input('satuan_hasil')),
            'margin_persen' => $this->normalizeNumber($request->input('margin_persen', 0)),
            'qty' => array_map(fn ($qty) => $this->normalizeNumber($qty), $request->input('qty', [])),
        ]);

        $data = $request->validate([
            'nama_formula' => ['required', 'string', 'max:255'],
            'qty_hasil' => ['required', 'numeric', 'min:0.001'],
            'satuan_hasil' => ['required', 'string', 'max:30'],
            'margin_persen' => ['required', 'numeric', 'min:0'],
            'aktif' => ['nullable', 'boolean'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'barang_bahan_id' => ['required', 'array', 'min:1'],
            'barang_bahan_id.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('barangs', 'id')->whereIn('jenis_barang', ['bahan_baku', 'bahan_penolong', 'barang_dagang']),
            ],
            'qty' => ['required', 'array', 'min:1'],
            'qty.*' => ['required', 'numeric', 'min:0.001'],
        ]);

        return $data;
    }

    private function formulaPayload(array $data): array
    {
        $totals = $this->calculateTotals($data);

        return [
            'nama_formula' => $data['nama_formula'],
            'qty_hasil' => $data['qty_hasil'],
            'satuan_hasil' => $data['satuan_hasil'],
            'total_biaya' => $totals['total_biaya'],
            'hpp' => $totals['hpp'],
            'margin_persen' => $data['margin_persen'],
            'harga_jual_rekomendasi' => $totals['harga_jual_rekomendasi'],
            'aktif' => (bool) ($data['aktif'] ?? false),
            'catatan' => $data['catatan'] ?? null,
        ];
    }

    private function syncItems(Formula $formula, array $data): void
    {
        $barangs = Barang::whereIn('id', $data['barang_bahan_id'])->get()->keyBy('id');

        foreach ($data['barang_bahan_id'] as $index => $barangId) {
            $barang = $barangs->get($barangId);
            $qty = (float) $data['qty'][$index];
            $hargaBeli = (float) $barang->harga_beli;

            $formula->items()->create([
                'barang_bahan_id' => $barang->id,
                'kode_barang' => $barang->kode_barang,
                'nama_barang' => $barang->nama_barang,
                'satuan' => $barang->satuan,
                'qty' => $qty,
                'harga_beli' => $hargaBeli,
                'subtotal' => $qty * $hargaBeli,
            ]);
        }
    }

    private function calculateTotals(array $data): array
    {
        $barangs = Barang::whereIn('id', $data['barang_bahan_id'])->get()->keyBy('id');
        $totalBiaya = 0;

        foreach ($data['barang_bahan_id'] as $index => $barangId) {
            $totalBiaya += (float) $data['qty'][$index] * (float) $barangs->get($barangId)->harga_beli;
        }

        $hpp = $totalBiaya / (float) $data['qty_hasil'];
        $hargaJualRekomendasi = $this->roundUpToThousand($hpp + ($hpp * ((float) $data['margin_persen'] / 100)));

        return [
            'total_biaya' => $totalBiaya,
            'hpp' => $hpp,
            'harga_jual_rekomendasi' => $hargaJualRekomendasi,
        ];
    }

    private function normalizeNumber(mixed $value): string
    {
        return str_replace(',', '.', trim((string) $value));
    }

    private function normalizeSatuan(mixed $value): string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : 'pcs';
    }

    private function roundUpToThousand(float $value): float
    {
        return ceil($value / 1000) * 1000;
    }
}
