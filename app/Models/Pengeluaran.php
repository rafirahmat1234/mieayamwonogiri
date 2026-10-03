<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    protected $table = 'pengeluarans';

    protected $fillable = [
        'tanggal',
        'keterangan',
        'jumlah',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah'  => 'integer',
    ];
}