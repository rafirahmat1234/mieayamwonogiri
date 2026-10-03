<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $fillable = [
        'nomor_pesanan',
        'tanggal',
        'total',
        'user_id',
        'transaksi_id',
    ];

    public function detail()
    {
        return $this->hasMany(PesananDetail::class, 'pesanan_id');
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
