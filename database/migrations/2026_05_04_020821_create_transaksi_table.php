<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->enum('tipe', ['pemasukan', 'pengeluaran']);
            $table->unsignedBigInteger('kategori_id')->nullable();
            $table->string('keterangan');
            $table->decimal('jumlah', 15, 2);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('transaksi');
    }
};