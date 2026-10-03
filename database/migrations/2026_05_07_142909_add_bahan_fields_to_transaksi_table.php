<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBahanFieldsToTransaksiTable extends Migration
{
    public function up()
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->unsignedBigInteger('bahan_id')->nullable()->after('kategori_id');
            $table->decimal('jumlah_bahan', 10, 2)->nullable()->after('bahan_id');
            $table->foreign('bahan_id')->references('id')->on('stok_bahan')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropForeign(['bahan_id']);
            $table->dropColumn(['bahan_id', 'jumlah_bahan']);
        });
    }
}