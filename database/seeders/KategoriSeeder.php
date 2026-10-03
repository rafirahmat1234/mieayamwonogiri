<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;
use App\Models\Transaksi;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        // Kategori Pemasukan
        $kategoris = [
            ['nama' => 'Penjualan Mie Ayam', 'tipe' => 'pemasukan'],
            ['nama' => 'Penjualan Minuman',  'tipe' => 'pemasukan'],
            ['nama' => 'Pendapatan Lain',    'tipe' => 'pemasukan'],
            ['nama' => 'Bahan Baku',         'tipe' => 'pengeluaran'],
            ['nama' => 'Gas & Listrik',      'tipe' => 'pengeluaran'],
            ['nama' => 'Gaji Karyawan',      'tipe' => 'pengeluaran'],
            ['nama' => 'Sewa Tempat',        'tipe' => 'pengeluaran'],
            ['nama' => 'Transportasi',       'tipe' => 'pengeluaran'],
            ['nama' => 'Lain-lain',          'tipe' => 'pengeluaran'],
        ];

        foreach ($kategoris as $k) {
            Kategori::create($k);
        }

        // Contoh Transaksi
        $transaksis = [
            ['tanggal' => now()->subDays(6), 'tipe' => 'pemasukan',   'kategori_id' => 1, 'keterangan' => 'Penjualan mie ayam pagi',    'jumlah' => 450000],
            ['tanggal' => now()->subDays(6), 'tipe' => 'pengeluaran', 'kategori_id' => 4, 'keterangan' => 'Beli daging ayam segar',     'jumlah' => 175000],
            ['tanggal' => now()->subDays(5), 'tipe' => 'pemasukan',   'kategori_id' => 1, 'keterangan' => 'Penjualan mie ayam',         'jumlah' => 520000],
            ['tanggal' => now()->subDays(5), 'tipe' => 'pemasukan',   'kategori_id' => 2, 'keterangan' => 'Penjualan minuman',          'jumlah' => 85000],
            ['tanggal' => now()->subDays(5), 'tipe' => 'pengeluaran', 'kategori_id' => 4, 'keterangan' => 'Beli mie & bahan baku',      'jumlah' => 120000],
            ['tanggal' => now()->subDays(4), 'tipe' => 'pemasukan',   'kategori_id' => 1, 'keterangan' => 'Penjualan mie ayam',         'jumlah' => 480000],
            ['tanggal' => now()->subDays(4), 'tipe' => 'pengeluaran', 'kategori_id' => 5, 'keterangan' => 'Beli gas LPG 2 tabung',      'jumlah' => 44000],
            ['tanggal' => now()->subDays(3), 'tipe' => 'pemasukan',   'kategori_id' => 1, 'keterangan' => 'Penjualan mie ayam ramai',   'jumlah' => 620000],
            ['tanggal' => now()->subDays(3), 'tipe' => 'pengeluaran', 'kategori_id' => 4, 'keterangan' => 'Belanja bahan baku pasar',   'jumlah' => 210000],
            ['tanggal' => now()->subDays(2), 'tipe' => 'pemasukan',   'kategori_id' => 1, 'keterangan' => 'Penjualan mie ayam',         'jumlah' => 500000],
            ['tanggal' => now()->subDays(2), 'tipe' => 'pengeluaran', 'kategori_id' => 6, 'keterangan' => 'Gaji karyawan mingguan',     'jumlah' => 300000],
            ['tanggal' => now()->subDays(1), 'tipe' => 'pemasukan',   'kategori_id' => 1, 'keterangan' => 'Penjualan mie ayam',         'jumlah' => 540000],
            ['tanggal' => now()->subDays(1), 'tipe' => 'pengeluaran', 'kategori_id' => 4, 'keterangan' => 'Bahan baku harian',          'jumlah' => 150000],
            ['tanggal' => now(),             'tipe' => 'pemasukan',   'kategori_id' => 1, 'keterangan' => 'Penjualan mie ayam hari ini','jumlah' => 460000],
            ['tanggal' => now(),             'tipe' => 'pengeluaran', 'kategori_id' => 4, 'keterangan' => 'Beli bahan baku pagi',       'jumlah' => 130000],
        ];

        foreach ($transaksis as $t) {
            Transaksi::create($t);
        }
    }
}