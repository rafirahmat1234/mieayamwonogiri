<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration ini awalnya dimaksudkan untuk menghapus kolom kategori_id
     * dan menambahkan kolom user_id ke tabel transaksi.
     *
     * Kedua perubahan itu sudah ada di database saat ini (sudah pernah
     * diterapkan sebelumnya), sehingga method up() dikosongkan supaya
     * migration ini tidak mencoba mengulang perubahan yang sudah ada.
     */
    public function up(): void
    {
        /* tidak ada perubahan, sudah diterapkan di database */
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->unsignedBigInteger('kategori_id')->nullable();
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
