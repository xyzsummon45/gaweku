<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BarangController extends Controller
{
    public function index()
    {
        $barangs = Barang::orderBy('id', 'desc')->get();

        return view('barang.index', compact('barangs'));
    }

    public function create()
    {
        return view('barang.create', [
            'barang' => new Barang(),
        ]);
    }

    public function store(Request $request)
    {
        Barang::create($this->validatedData($request));

        return redirect()
            ->route('barang.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Barang $barang)
    {
        return view('barang.edit', compact('barang'));
    }

    public function update(Request $request, Barang $barang)
    {
        $barang->update($this->validatedData($request, $barang));

        return redirect()
            ->route('barang.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Barang $barang)
    {
        $barang->delete();

        return redirect()
            ->route('barang.index')
            ->with('success', 'Barang berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xls,xlsx', 'max:5120'],
        ]);

        if (! class_exists(IOFactory::class)) {
            return back()->withErrors([
                'file' => 'Import Excel membutuhkan package phpoffice/phpspreadsheet.',
            ]);
        }

        $sheet = IOFactory::load($request->file('file')->getRealPath())->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        if (count($rows) < 2) {
            return back()->withErrors([
                'file' => 'File Excel harus memiliki header dan minimal satu baris data.',
            ]);
        }

        $header = array_map(
            fn ($value) => strtolower(trim((string) $value)),
            array_shift($rows)
        );

        $columns = array_flip($header);
        $requiredColumns = ['kode_barang', 'nama_barang', 'harga_beli', 'harga_jual', 'stok'];
        $missingColumns = array_diff($requiredColumns, array_keys($columns));

        if ($missingColumns !== []) {
            return back()->withErrors([
                'file' => 'Kolom Excel belum lengkap: '.implode(', ', $missingColumns).'.',
            ]);
        }

        $imported = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $line = $index + 2;
            $data = [
                'kode_barang' => trim((string) $row[$columns['kode_barang']]),
                'nama_barang' => trim((string) $row[$columns['nama_barang']]),
                'harga_beli' => $this->normalizeNumber($row[$columns['harga_beli']]),
                'harga_jual' => $this->normalizeNumber($row[$columns['harga_jual']]),
                'stok' => $this->normalizeInteger($row[$columns['stok']]),
            ];

            if (implode('', array_map('strval', $data)) === '') {
                continue;
            }

            $validator = validator($data, $this->rules(allowExistingCode: true));

            if ($validator->fails()) {
                $errors[] = "Baris {$line}: ".$validator->errors()->first();
                continue;
            }

            Barang::updateOrCreate(
                ['kode_barang' => $data['kode_barang']],
                $data
            );

            $imported++;
        }

        if ($errors !== []) {
            return back()
                ->with('success', "{$imported} barang berhasil diimport.")
                ->withErrors(['file' => implode(' ', array_slice($errors, 0, 5))]);
        }

        return redirect()
            ->route('barang.index')
            ->with('success', "{$imported} barang berhasil diimport.");
    }

    private function validatedData(Request $request, ?Barang $barang = null): array
    {
        return $request->validate($this->rules($barang));
    }

    private function rules(?Barang $barang = null, bool $allowExistingCode = false): array
    {
        $codeRules = ['required', 'string', 'max:50'];

        if (! $allowExistingCode) {
            $codeRules[] = Rule::unique('barangs', 'kode_barang')->ignore($barang);
        }

        return [
            'kode_barang' => $codeRules,
            'nama_barang' => ['required', 'string', 'max:255'],
            'harga_beli' => ['required', 'numeric', 'min:0'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
            'stok' => ['required', 'integer', 'min:0'],
        ];
    }

    private function normalizeNumber(mixed $value): string
    {
        return str_replace(',', '.', trim((string) $value));
    }

    private function normalizeInteger(mixed $value): string
    {
        return (string) (int) trim((string) $value);
    }
}
