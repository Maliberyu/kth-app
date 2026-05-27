<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lahan_layanan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sumber_air_id');
            $table->string('nama_lahan');
            $table->enum('tipe_lahan', ['sawah', 'kolam', 'kebun']);
            $table->decimal('luas_ha', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lahan_layanan');
    }
};
