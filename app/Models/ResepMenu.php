<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResepMenu extends Model
{
    protected $table = 'resep_menu';

    protected $fillable = [
        'menu_id',
        'stok_bahan_id',
        'jumlah_terpakai',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function stokBahan()
    {
        return $this->belongsTo(StokBahan::class, 'stok_bahan_id');
    }
}
