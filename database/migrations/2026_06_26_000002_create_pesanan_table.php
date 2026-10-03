<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pesanan')->unique(); // contoh: PSN-20260626-0001
            $table->date('tanggal');
            $table->decimal('total', 12, 2);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            // Menyimpan referensi ke transaksi keuangan yang otomatis dibuat
            $table->foreignId('transaksi_id')->nullable()->constrained('transaksi')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
