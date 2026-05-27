<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('komoditas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kups_id');
            $table->string('nama_komoditas');
            $table->decimal('volume', 10, 2)->default(0);
            $table->string('satuan'); // Kg, Ikat, Batang, Liter, Buah
            $table->unsignedBigInteger('harga')->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komoditas');
    }
};
