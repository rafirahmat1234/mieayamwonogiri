<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokBahan extends Model
{
    protected $table = 'stok_bahan';
    protected $fillable = [
        'nama_bahan',
        'satuan',
        'stok_saat_ini',
        'stok_minimum',
        'harga_per_satuan',
    ];
}