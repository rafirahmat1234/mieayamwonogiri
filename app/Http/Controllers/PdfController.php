<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Transaksi;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\Produk;

class PdfController extends Controller
{
    // ============================================================
    // 1. LAPORAN KEUANGAN (semua transaksi)
    // ============================================================
 public function labaRugi(Request $request)
{
    $bulan = $request->bulan ?? now()->month;
    $tahun = $request->tahun ?? now()->year;

    $pemasukan = Transaksi::where('tipe', 'pemasukan')
                          ->whereMonth('tanggal', $bulan)
                          ->whereYear('tanggal', $tahun)
                          ->orderBy('tanggal')
                          ->get();

    $pengeluaran = Transaksi::where('tipe', 'pengeluaran')
                            ->whereMonth('tanggal', $bulan)
                            ->whereYear('tanggal', $tahun)
                            ->orderBy('tanggal')
                            ->get();

    $totalPemasukan   = $pemasukan->sum('jumlah');
    $totalPengeluaran = $pengeluaran->sum('jumlah');
    $labaRugi         = $totalPemasukan - $totalPengeluaran;

    $namaBulan = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F');

    $pdf = Pdf::loadView('pdf.laba-rugi', compact(
        'pemasukan', 'pengeluaran',
        'totalPemasukan', 'totalPengeluaran',
        'labaRugi', 'namaBulan', 'tahun'
    ))->setPaper('a4', 'portrait');

    return $pdf->download("laba-rugi-{$namaBulan}-{$tahun}.pdf");
}
}