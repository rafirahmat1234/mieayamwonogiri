<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus';

    protected $fillable = [
        'nama_menu',
        'harga',
        'kategori',
        'aktif',
    ];

    public function pesananDetail()
    {
        return $this->hasMany(PesananDetail::class, 'menu_id');
    }
}
