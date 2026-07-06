<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $keyword = trim($filters['q'] ?? '');

        $suppliers = Supplier::query()
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('nama_supplier', 'like', "%{$keyword}%")
                        ->orWhere('no_hp', 'like', "%{$keyword}%")
                        ->orWhere('alamat', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('nama_supplier')
            ->paginate(10)
            ->withQueryString();

        return view('supplier.index', [
            'suppliers' => $suppliers,
            'filters' => [
                'q' => $keyword,
            ],
        ]);
    }

    public function create()
    {
        return view('supplier.create', [
            'supplier' => new Supplier(),
        ]);
    }

    public function store(Request $request)
    {
        Supplier::create($this->validatedData($request));

        return redirect()
            ->route('supplier.index')
            ->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function edit(Supplier $supplier)
    {
        return view('supplier.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $supplier->update($this->validatedData($request));

        return redirect()
            ->route('supplier.index')
            ->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()
            ->route('supplier.index')
            ->with('success', 'Supplier berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'nama_supplier' => ['required', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:50'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
