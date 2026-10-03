<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\ResepMenu;
use App\Models\StokBahan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    /**
     * Tampilkan halaman kasir (pilih menu, keranjang)
     */
    public function index()
    {
        $menus = Menu::where('aktif', true)->orderBy('kategori')->orderBy('nama_menu')->get();
        return view('pos.index', compact('menus'));
    }

    /**
     * Simpan pesanan baru dari keranjang.
     * Request berisi array items: [{menu_id, jumlah}, ...]
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        try {
            // Semua operasi dibungkus transaction DB supaya konsisten:
            // kalau ada satu langkah gagal (termasuk stok kurang), semua dibatalkan (rollback).
            $pesanan = DB::transaction(function () use ($validated) {

                $total = 0;
                $detailItems = [];

                // kebutuhanBahan: [stok_bahan_id => total jumlah yang harus dikurangi]
                $kebutuhanBahan = [];

                foreach ($validated['items'] as $item) {
                    $menu = Menu::findOrFail($item['menu_id']);
                    $subtotal = $menu->harga * $item['jumlah'];
                    $total += $subtotal;

                    $detailItems[] = [
                        'menu_id' => $menu->id,
                        'nama_menu' => $menu->nama_menu,
                        'jumlah' => $item['jumlah'],
                        'harga_satuan' => $menu->harga,
                        'subtotal' => $subtotal,
                    ];

                    // Akumulasi kebutuhan bahan berdasarkan resep menu ini.
                    // Kalau menu belum punya resep sama sekali, tidak ada bahan
                    // yang dikurangi untuk menu itu (tidak error, cuma tidak ngurangi stok).
                    $resepList = ResepMenu::where('menu_id', $menu->id)->get();
                    foreach ($resepList as $resep) {
                        $butuh = $resep->jumlah_terpakai * $item['jumlah'];
                        $kebutuhanBahan[$resep->stok_bahan_id] =
                            ($kebutuhanBahan[$resep->stok_bahan_id] ?? 0) + $butuh;
                    }
                }

                // --- CEK STOK SEBELUM LANJUT ---
                // lockForUpdate() mengunci baris stok_bahan yang dipakai, supaya kalau
                // ada 2 kasir input bersamaan, stok tetap dihitung dengan benar (anti race condition).
                $stokBahanTerkunci = [];
                $kurang = [];

                foreach ($kebutuhanBahan as $stokBahanId => $jumlahDibutuhkan) {
                    $stokBahan = StokBahan::where('id', $stokBahanId)->lockForUpdate()->first();
                    $stokBahanTerkunci[$stokBahanId] = $stokBahan;

                    if (!$stokBahan || $stokBahan->stok_saat_ini < $jumlahDibutuhkan) {
                        $tersisa = self::formatAngka($stokBahan->stok_saat_ini ?? 0);
                        $butuh = self::formatAngka($jumlahDibutuhkan);
                        $satuan = $stokBahan->satuan ?? '';
                        $namaBahan = $stokBahan->nama_bahan ?? ('bahan #' . $stokBahanId);
                        $kurang[] = "{$namaBahan} (tersisa {$tersisa} {$satuan}, butuh {$butuh} {$satuan})";
                    }
                }

                if (!empty($kurang)) {
                    // Lempar exception supaya transaction di-rollback otomatis:
                    // tidak ada transaksi/pesanan/stok yang berubah sama sekali.
                    throw new \RuntimeException('Stok bahan tidak cukup: ' . implode('; ', $kurang));
                }

                // Generate nomor pesanan unik: PSN-20260626-0001
                $tanggalHariIni = now()->format('Ymd');
                $countHariIni = Pesanan::whereDate('created_at', now()->toDateString())->count() + 1;
                $nomorPesanan = 'PSN-' . $tanggalHariIni . '-' . str_pad($countHariIni, 4, '0', STR_PAD_LEFT);

                // Ringkasan lengkap menu, mis: "Mie Ayam x2, Es Jeruk x1, Mie Ayam Chili Oil x1"
                $ringkasanMenu = collect($detailItems)
                    ->map(fn($d) => $d['nama_menu'] . ' x' . $d['jumlah'])
                    ->implode(', ');

                // Kolom 'keterangan' sekarang sudah diperbesar jadi VARCHAR(255)
                // (lihat migrasi/ALTER TABLE), jadi rincian menu lengkap bisa
                // langsung disimpan di sini tanpa perlu dipotong/disingkat lagi.
                $keteranganLengkap = $ringkasanMenu;

                // Tetap jaga-jaga kalau suatu saat ada pesanan dengan SANGAT banyak item
                // sehingga melebihi batas kolom (255 karakter).
                if (strlen($keteranganLengkap) > 255) {
                    $keteranganLengkap = substr($keteranganLengkap, 0, 252) . '...';
                }

                // 1. Catat ke tabel transaksi (tipe pemasukan)
                $transaksi = Transaksi::create([
                    'user_id' => auth()->id(),
                    'tanggal' => now()->toDateString(),
                    'tipe' => 'pemasukan',
                    'keterangan' => $keteranganLengkap,
                    'jumlah' => $total,
                    'catatan' => 'Penjualan POS #' . $nomorPesanan . ': ' . $ringkasanMenu,
                    'bahan_id' => null,
                    'jumlah_bahan' => null,
                ]);

                // 2. Buat pesanan, terhubung ke transaksi yang baru dibuat
                $pesanan = Pesanan::create([
                    'nomor_pesanan' => $nomorPesanan,
                    'tanggal' => now()->toDateString(),
                    'total' => $total,
                    'user_id' => auth()->id(),
                    'transaksi_id' => $transaksi->id,
                ]);

                // 3. Simpan detail item pesanan
                foreach ($detailItems as $d) {
                    PesananDetail::create([
                        'pesanan_id' => $pesanan->id,
                        'menu_id' => $d['menu_id'],
                        'jumlah' => $d['jumlah'],
                        'harga_satuan' => $d['harga_satuan'],
                        'subtotal' => $d['subtotal'],
                    ]);
                }

                // 4. Kurangi stok bahan sesuai resep (baris sudah dikunci & sudah dipastikan cukup di atas)
                foreach ($kebutuhanBahan as $stokBahanId => $jumlahDibutuhkan) {
                    $stokBahan = $stokBahanTerkunci[$stokBahanId];
                    $stokBahan->stok_saat_ini = $stokBahan->stok_saat_ini - $jumlahDibutuhkan;
                    $stokBahan->save();
                }

                return $pesanan;
            });
        } catch (\RuntimeException $e) {
            // Stok kurang -> balik ke halaman POS dengan pesan error.
            // Tidak ada data yang tersimpan (sudah di-rollback oleh DB::transaction).
            return back()->withInput()->with('error', $e->getMessage());
        }

        // Redirect ke Dashboard supaya langsung kelihatan update real-time.
        return redirect()
            ->route('dashboard')
            ->with('success', 'Pesanan ' . $pesanan->nomor_pesanan . ' berhasil disimpan! Total: Rp ' . number_format($pesanan->total, 0, ',', '.'));
    }

    /**
     * Format angka tanpa nol di belakang koma yang tidak perlu (mis. 1.50 -> 1.5, 3.00 -> 3)
     */
    private static function formatAngka($angka)
    {
        return rtrim(rtrim(number_format((float) $angka, 2, '.', ''), '0'), '.');
    }

    /**
     * Tampilkan struk pesanan (tetap disediakan, untuk dicetak manual kalau perlu)
     */
    public function struk($id)
    {
        $pesanan = Pesanan::with('detail.menu')->findOrFail($id);
        return view('pos.struk', compact('pesanan'));
    }

    /**
     * Riwayat pesanan POS
     */
    public function riwayat(Request $request)
    {
        $bulan = $request->bulan ?? date('Y-m');

        $pesanan = Pesanan::with('detail')
            ->whereYear('tanggal', substr($bulan, 0, 4))
            ->whereMonth('tanggal', substr($bulan, 5, 2))
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pos.riwayat', compact('pesanan', 'bulan'));
    }
}