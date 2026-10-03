<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('stok_bahan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bahan');
            $table->string('satuan');
            $table->decimal('stok_saat_ini', 10, 2)->default(0);
            $table->decimal('stok_minimum', 10, 2)->default(0);
            $table->decimal('harga_per_satuan', 15, 2)->default(0);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('stok_bahan');
    }
};