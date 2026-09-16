<?php

namespace App\Http\Controllers;

use App\Models\TransaksiItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanPenjualanController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->reportData($request);

        return view('laporan.penjualan', $data);
    }

    public function downloadPdf(Request $request)
    {
        $data = $this->reportData($request);
        $filename = 'laporan-laba-rugi-jual-'.$data['filters']['tanggal_mulai'].'-'.$data['filters']['tanggal_selesai'].'.pdf';

        return Pdf::loadView('laporan.penjualan-pdf', $data)
            ->setPaper('a4', 'landscape')
            ->download($filename);
    }

    private function reportData(Request $request): array
    {
        $filters = $request->validate([
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        $tanggalMulai = $filters['tanggal_mulai'] ?? now()->toDateString();
        $tanggalSelesai = $filters['tanggal_selesai'] ?? $tanggalMulai;

        $items = TransaksiItem::with('transaksi')
            ->whereHas('transaksi', function ($query) use ($tanggalMulai, $tanggalSelesai) {
                $query->whereDate('tanggal', '>=', $tanggalMulai)
                    ->whereDate('tanggal', '<=', $tanggalSelesai);
            })
            ->join('transaksis', 'transaksi_items.transaksi_id', '=', 'transaksis.id')
            ->orderBy('transaksis.tanggal')
            ->orderBy('transaksi_items.id')
            ->select('transaksi_items.*')
            ->get();

        return [
            'items' => $items,
            'summary' => [
                'total_penjualan' => (float) $items->sum('subtotal'),
                'total_modal' => (float) $items->sum('subtotal_modal'),
                'laba_kotor' => (float) $items->sum('laba_kotor'),
            ],
            'filters' => [
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
            ],
        ];
    }
}
