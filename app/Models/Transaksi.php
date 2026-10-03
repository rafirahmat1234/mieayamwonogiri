<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    
    protected $fillable = [
    'user_id',      // ← tambahkan ini
    'tanggal',
    'tipe', 
    'keterangan',
    'jumlah',
    'catatan',
    'bahan_id',
    'jumlah_bahan',
    // 'kategori_id' ← hapus ini, sudah tidak ada di tabel
];

    public function kategori()
    {
        return $this->belongsTo(\App\Models\Kategori::class, 'kategori_id');
    }

    public function bahan()
    {
        return $this->belongsTo(\App\Models\StokBahan::class, 'bahan_id');
    }

    public function user()
{
    return $this->belongsTo(\App\Models\User::class, 'user_id');
}
}