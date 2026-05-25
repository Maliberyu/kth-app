<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usaha_transaksi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kegiatan_usaha_id');
            $table->string('tipe');      // pemasukan | pengeluaran
            $table->string('kategori');
            $table->unsignedBigInteger('jumlah');
            $table->date('tanggal');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usaha_transaksi');
    }
};
