<?php

namespace App\Http\Controllers;

use App\Models\StokBahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StokBahanController extends Controller
{
    public function index()
    {
        $stok = StokBahan::orderBy('nama_bahan')->get();
        return view('stok-bahan.index', compact('stok'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('stok-bahan.index')
                ->with('error', 'Admin tidak dapat menambah stok bahan!');
        }

        $request->validate([
            'nama_bahan'       => 'required|string|max:100',
            'satuan'           => 'required|string|max:30',
            'stok_saat_ini'    => 'required|numeric|min:0',
            'stok_minimum'     => 'required|numeric|min:0',
            'harga_per_satuan' => 'required|numeric|min:0',
        ]);

        StokBahan::create($request->all());
        return redirect()->route('stok-bahan.index')
            ->with('success', 'Bahan berhasil ditambahkan!');
    }

    public function update(Request $request, StokBahan $stokBahan)
    {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('stok-bahan.index')
                ->with('error', 'Admin tidak dapat mengubah stok bahan!');
        }

        $request->validate([
            'stok_saat_ini' => 'required|numeric|min:0',
        ]);

        $stokBahan->update(['stok_saat_ini' => $request->stok_saat_ini]);
        return redirect()->route('stok-bahan.index')
            ->with('success', 'Stok berhasil diupdate!');
    }

    public function destroy(StokBahan $stokBahan)
    {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('stok-bahan.index')
                ->with('error', 'Admin tidak dapat menghapus stok bahan!');
        }

        $stokBahan->delete();
        return redirect()->route('stok-bahan.index')
            ->with('success', 'Bahan berhasil dihapus!');
    }
}