<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pengukuran_debit', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sumber_air_id');
            $table->date('tanggal');
            $table->decimal('debit', 8, 3);
            $table->string('satuan')->default('liter/detik');
            $table->string('nama_petugas');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('foto')->nullable();
            $table->text('catatan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengukuran_debit');
    }
};
