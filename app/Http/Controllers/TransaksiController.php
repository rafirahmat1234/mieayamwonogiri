<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Kategori;
use App\Models\StokBahan;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function pemasukan(Request $request)
    {
        $filterPeriode = $request->periode ?? 'bulan';
        $bulan  = $request->bulan  ?? date('Y-m');
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $tahun  = $request->tahun  ?? date('Y');

        $query = Transaksi::with('kategori')->where('tipe', 'pemasukan');
        $query = $this->applyFilter($query, $filterPeriode, $bulan, $tanggal, $tahun);
        $transaksi = $query->orderBy('tanggal', 'desc')->get();

        $totalMasuk = $this->getTotal('pemasukan', $filterPeriode, $bulan, $tanggal, $tahun);
        $kategoris  = Kategori::where('tipe', 'pemasukan')->get();

        return view('transaksi.pemasukan', compact(
            'transaksi', 'totalMasuk', 'kategoris',
            'filterPeriode', 'bulan', 'tanggal', 'tahun'
        ));
    }

    public function pengeluaran(Request $request)
    {
        $filterPeriode = $request->periode ?? 'bulan';
        $bulan  = $request->bulan  ?? date('Y-m');
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $tahun  = $request->tahun  ?? date('Y');

        $query = Transaksi::with(['kategori', 'bahan'])->where('tipe', 'pengeluaran');
        $query = $this->applyFilter($query, $filterPeriode, $bulan, $tanggal, $tahun);
        $transaksi = $query->orderBy('tanggal', 'desc')->get();

        $totalKeluar = $this->getTotal('pengeluaran', $filterPeriode, $bulan, $tanggal, $tahun);
        $kategoris   = Kategori::where('tipe', 'pengeluaran')->get();

        return view('transaksi.pengeluaran', compact(
            'transaksi', 'totalKeluar', 'kategoris',
            'filterPeriode', 'bulan', 'tanggal', 'tahun'
        ));
    }

    private function applyFilter($query, $periode, $bulan, $tanggal, $tahun)
    {
        if ($periode === 'hari')
            return $query->whereDate('tanggal', $tanggal);
        if ($periode === 'bulan')
            return $query->whereYear('tanggal', substr($bulan,0,4))->whereMonth('tanggal', substr($bulan,5,2));
        if ($periode === 'tahun')
            return $query->whereYear('tanggal', $tahun);
        return $query;
    }

    private function getTotal($tipe, $periode, $bulan, $tanggal, $tahun)
    {
        $q = Transaksi::where('tipe', $tipe);
        return $this->applyFilter($q, $periode, $bulan, $tanggal, $tahun)->sum('jumlah');
    }

    public function store(Request $request)
{
    $request->validate([
        'tanggal'    => 'required|date',
        'tipe'       => 'required|in:pemasukan,pengeluaran',
        'keterangan' => 'required|string|max:255',
        'jumlah'     => 'required|numeric|min:0',
    ]);

    Transaksi::create(array_merge($request->all(), [
        'user_id' => auth()->id(),
    ]));

    // ✅ Kurangi stok bahan untuk PEMASUKAN maupun PENGELUARAN
    if ($request->bahan_id && $request->jumlah_bahan > 0) {
        $bahan = StokBahan::find($request->bahan_id);
        if ($bahan) {
            $bahan->stok_saat_ini -= $request->jumlah_bahan;
            $bahan->save();
        }
    }

    $route = $request->tipe === 'pemasukan' ? 'pemasukan.index' : 'pengeluaran.index';
    return redirect()->route($route)->with('success', 'Transaksi berhasil disimpan!');
}

    public function destroy(Transaksi $transaksi)
    {
        $tipe = $transaksi->tipe;

        // ✅ Kembalikan stok bahan saat pengeluaran dihapus
        if ($tipe === 'pengeluaran' && $transaksi->bahan_id && $transaksi->jumlah_bahan > 0) {
            $bahan = StokBahan::find($transaksi->bahan_id);
            if ($bahan) {
                $bahan->stok_saat_ini += $transaksi->jumlah_bahan;
                $bahan->save();
            }
        }

        $transaksi->delete();
        $route = $tipe === 'pemasukan' ? 'pemasukan.index' : 'pengeluaran.index';
        return redirect()->route($route)->with('success', 'Transaksi berhasil dihapus!');
    }
}