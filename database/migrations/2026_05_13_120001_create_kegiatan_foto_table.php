<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kegiatan_foto', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kegiatan_id');
            $table->string('file_path');
            $table->string('keterangan')->nullable();
            $table->tinyInteger('urutan')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kegiatan_foto');
    }
};
