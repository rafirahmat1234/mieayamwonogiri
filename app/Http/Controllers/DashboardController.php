<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\StokBahan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $tahun = date('Y');
        $bln   = date('m');

        // ── Total KESELURUHAN (semua waktu) ──────────
        $totalMasukAll  = Transaksi::where('tipe', 'pemasukan')->sum('jumlah');
        $totalKeluarAll = Transaksi::where('tipe', 'pengeluaran')->sum('jumlah');
        $labaAll        = $totalMasukAll - $totalKeluarAll;

        // ── Total BULAN INI ──────────────────────────
        $totalMasukBulan  = Transaksi::whereYear('tanggal', $tahun)
                            ->whereMonth('tanggal', $bln)
                            ->where('tipe', 'pemasukan')->sum('jumlah');

        $totalKeluarBulan = Transaksi::whereYear('tanggal', $tahun)
                            ->whereMonth('tanggal', $bln)
                            ->where('tipe', 'pengeluaran')->sum('jumlah');

        $labaBulan = $totalMasukBulan - $totalKeluarBulan;

        // ── Alias untuk blade ────────────────────────
        $totalMasuk  = $totalMasukBulan;
        $totalKeluar = $totalKeluarBulan;
        $laba        = $labaBulan;

        // ── Grafik 7 hari terakhir ───────────────────
        $grafik = Transaksi::selectRaw('tanggal,
                    SUM(CASE WHEN tipe="pemasukan" THEN jumlah ELSE 0 END) as masuk,
                    SUM(CASE WHEN tipe="pengeluaran" THEN jumlah ELSE 0 END) as keluar')
                    ->where('tanggal', '>=', now()->subDays(6)->toDateString())
                    ->groupBy('tanggal')
                    ->orderBy('tanggal')
                    ->get();

        // ── Transaksi terbaru ────────────────────────
        $terbaru = Transaksi::with('kategori')
                    ->orderBy('tanggal', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit(8)->get();

        // ── Stok hampir habis ────────────────────────
        $stokWarning = StokBahan::whereRaw('stok_saat_ini <= stok_minimum')->get();

        return view('dashboard', compact(
            'totalMasukAll', 'totalKeluarAll', 'labaAll',
            'totalMasukBulan', 'totalKeluarBulan', 'labaBulan',
            'totalMasuk', 'totalKeluar', 'laba',
            'grafik', 'terbaru', 'stokWarning'
        ));
    }
}