<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->bulan ?? date('Y-m');
        $tahun = substr($bulan, 0, 4);
        $bln   = substr($bulan, 5, 2);

        // ===== LABA BERSIH =====
        // Total pemasukan (semua, termasuk dari POS karena POS otomatis
        // mencatat ke tabel transaksi dengan tipe = pemasukan)
        $totalMasuk = Transaksi::whereYear('tanggal', $tahun)
                        ->whereMonth('tanggal', $bln)
                        ->where('tipe', 'pemasukan')
                        ->sum('jumlah');

        // Pengeluaran dipecah: belanja stok bahan (bahan_id terisi)
        // vs biaya operasional murni (bahan_id kosong)
        $totalBelanjaStok = Transaksi::whereYear('tanggal', $tahun)
                        ->whereMonth('tanggal', $bln)
                        ->where('tipe', 'pengeluaran')
                        ->whereNotNull('bahan_id')
                        ->sum('jumlah');

        $totalBiayaOperasional = Transaksi::whereYear('tanggal', $tahun)
                        ->whereMonth('tanggal', $bln)
                        ->where('tipe', 'pengeluaran')
                        ->whereNull('bahan_id')
                        ->sum('jumlah');

        $totalKeluar = $totalBelanjaStok + $totalBiayaOperasional;

        // Laba bersih = pendapatan - biaya operasional saja
        $labaBersih = $totalMasuk - $totalBiayaOperasional;

        // ===== ARUS KAS =====
        // Arus kas BEDA dari laba bersih: ini murni pergerakan uang fisik,
        // jadi SEMUA pengeluaran dihitung (termasuk belanja stok),
        // karena uang memang benar-benar keluar dari kas bulan itu.
        $arusKasMasuk  = $totalMasuk;
        $arusKasKeluar = $totalKeluar; // semua pengeluaran, tanpa kecuali
        $arusKasBersih = $arusKasMasuk - $arusKasKeluar;

        // Saldo kas awal bulan = akumulasi arus kas bersih dari semua bulan sebelumnya
        $saldoAwal = Transaksi::where('tanggal', '<', $tahun . '-' . $bln . '-01')
                        ->selectRaw("SUM(CASE WHEN tipe='pemasukan' THEN jumlah ELSE -jumlah END) as saldo")
                        ->value('saldo') ?? 0;

        $saldoAkhir = $saldoAwal + $arusKasBersih;

        // Arus kas harian (untuk grafik/tabel rincian)
        $arusKasHarian = Transaksi::whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bln)
                    ->selectRaw('tanggal,
                        SUM(CASE WHEN tipe="pemasukan" THEN jumlah ELSE 0 END) as masuk,
                        SUM(CASE WHEN tipe="pengeluaran" THEN jumlah ELSE 0 END) as keluar')
                    ->groupBy('tanggal')
                    ->orderBy('tanggal')
                    ->get();

        // Tambahkan saldo berjalan (running balance) per hari
        $saldoBerjalan = $saldoAwal;
        $arusKasHarian = $arusKasHarian->map(function ($row) use (&$saldoBerjalan) {
            $saldoBerjalan += ($row->masuk - $row->keluar);
            $row->saldo_berjalan = $saldoBerjalan;
            return $row;
        });

        // ===== RINCIAN PER KETERANGAN (existing, dipertahankan) =====
        $perKategori = Transaksi::whereYear('tanggal', $tahun)
                        ->whereMonth('tanggal', $bln)
                        ->selectRaw('keterangan, tipe, SUM(jumlah) as total, COUNT(*) as jumlah_transaksi,
                            (CASE WHEN bahan_id IS NOT NULL THEN 1 ELSE 0 END) as is_belanja_stok')
                        ->groupBy('keterangan', 'tipe', 'is_belanja_stok')
                        ->get();

        // Harian untuk grafik pemasukan/pengeluaran (existing, dipertahankan)
        $harian = $arusKasHarian; // sama datanya, sudah termasuk saldo_berjalan

        return view('laporan.index', compact(
            'bulan',
            'totalMasuk', 'totalKeluar', 'totalBelanjaStok', 'totalBiayaOperasional', 'labaBersih',
            'arusKasMasuk', 'arusKasKeluar', 'arusKasBersih', 'saldoAwal', 'saldoAkhir', 'arusKasHarian',
            'perKategori', 'harian'
        ));
    }
}
