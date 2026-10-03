<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemasukan extends Model
{
    protected $table = 'pemasukas';

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