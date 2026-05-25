<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usaha_harian_foto', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usaha_harian_id');
            $table->string('file_path');
            $table->string('keterangan')->nullable();
            $table->tinyInteger('urutan')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usaha_harian_foto');
    }
};
