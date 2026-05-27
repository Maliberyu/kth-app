<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kampung_layanan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bak_tampung_id');
            $table->string('nama_kampung');
            $table->unsignedInteger('jumlah_kk')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kampung_layanan');
    }
};
