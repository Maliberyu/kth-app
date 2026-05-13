<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kegiatan_peserta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatans')->onDelete('cascade');
            $table->string('nama_lengkap');
            $table->string('jabatan')->nullable();
            $table->string('asal_instansi')->nullable();
            $table->string('email')->nullable();
            $table->string('no_hp', 20);
            $table->dateTime('waktu_hadir');
            $table->timestamps();

            // Cegah submit ganda: satu nomor HP hanya bisa daftar sekali per kegiatan
            $table->unique(['kegiatan_id', 'no_hp']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kegiatan_peserta');
    }
};
