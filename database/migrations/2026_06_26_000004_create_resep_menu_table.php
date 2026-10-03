<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resep_menu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->foreignId('stok_bahan_id')->constrained('stok_bahan')->onDelete('cascade');
            // jumlah_terpakai pakai presisi sama seperti stok_saat_ini di tabel stok_bahan (10,2)
            $table->decimal('jumlah_terpakai', 10, 2);
            $table->timestamps();

            // 1 menu tidak boleh punya 2 baris resep untuk bahan yang sama
            $table->unique(['menu_id', 'stok_bahan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resep_menu');
    }
};
