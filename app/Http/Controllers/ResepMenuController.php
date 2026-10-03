<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\ResepMenu;
use App\Models\StokBahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResepMenuController extends Controller
{
    /**
     * Daftar semua menu AKTIF beserta status resepnya (sudah diatur / belum).
     * Menu non-aktif (misal hasil duplikat yang sudah dinonaktifkan) tidak ditampilkan,
     * konsisten dengan halaman Kasir POS yang juga hanya menampilkan menu aktif.
     */
    public function index()
    {
        $menus = Menu::where('aktif', true)->orderBy('kategori')->orderBy('nama_menu')->get();

        // Hitung jumlah bahan per menu dalam 1 query saja (lebih efisien daripada query per baris)
        $jumlahResepPerMenu = ResepMenu::select('menu_id')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('menu_id')
            ->pluck('total', 'menu_id');

        return view('resep.index', compact('menus', 'jumlahResepPerMenu'));
    }

    /**
     * Form atur resep untuk 1 menu: pilih bahan apa saja & berapa jumlahnya per porsi.
     */
    public function edit(Menu $menu)
    {
        $stokBahanList = StokBahan::orderBy('nama_bahan')->get();

        // Resep yang sudah ada untuk menu ini -> dipetakan: stok_bahan_id => jumlah_terpakai
        $resepSaatIni = ResepMenu::where('menu_id', $menu->id)
            ->pluck('jumlah_terpakai', 'stok_bahan_id');

        return view('resep.edit', compact('menu', 'stokBahanList', 'resepSaatIni'));
    }

    /**
     * Simpan resep untuk 1 menu.
     * Strategi paling aman & sederhana: hapus semua resep lama untuk menu ini,
     * lalu simpan ulang dari form (replace total, bukan update satu-satu).
     */
    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'bahan' => 'array',
            'bahan.*.dipakai' => 'nullable',
            'bahan.*.jumlah' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $menu) {
            ResepMenu::where('menu_id', $menu->id)->delete();

            foreach ($validated['bahan'] ?? [] as $stokBahanId => $data) {
                $dicentang = isset($data['dipakai']);
                $jumlah = $data['jumlah'] ?? 0;

                if ($dicentang && $jumlah > 0) {
                    ResepMenu::create([
                        'menu_id' => $menu->id,
                        'stok_bahan_id' => $stokBahanId,
                        'jumlah_terpakai' => $jumlah,
                    ]);
                }
            }
        });

        return redirect()
            ->route('resep.index')
            ->with('success', 'Resep untuk menu "' . $menu->nama_menu . '" berhasil disimpan.');
    }

    /**
     * Tambah bahan baru langsung dari halaman Atur Resep (tanpa pindah ke halaman Stok Bahan).
     * Dipanggil via AJAX (fetch) dari form kecil "Tambah Bahan Baru" di resep/edit.blade.php.
     * Mengembalikan JSON supaya baris baru bisa langsung disisipkan ke tabel tanpa reload halaman.
     */
    public function storeBahanBaru(Request $request)
    {
        $validated = $request->validate([
            'nama_bahan' => 'required|string|max:25|unique:stok_bahan,nama_bahan',
            'satuan' => 'required|string|max:25',
            'stok_saat_ini' => 'nullable|numeric|min:0',
            'stok_minimum' => 'nullable|numeric|min:0',
            'harga_per_satuan' => 'nullable|numeric|min:0',
        ], [
            'nama_bahan.required' => 'Nama bahan wajib diisi.',
            'nama_bahan.unique' => 'Nama bahan ini sudah ada di Stok Bahan.',
            'satuan.required' => 'Satuan wajib diisi.',
        ]);

        $bahan = StokBahan::create([
            'nama_bahan' => $validated['nama_bahan'],
            'satuan' => $validated['satuan'],
            'stok_saat_ini' => $validated['stok_saat_ini'] ?? 0,
            'stok_minimum' => $validated['stok_minimum'] ?? 0,
            'harga_per_satuan' => $validated['harga_per_satuan'] ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'bahan' => $bahan,
        ]);
    }
}