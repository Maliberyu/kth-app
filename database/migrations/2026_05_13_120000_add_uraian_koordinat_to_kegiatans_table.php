<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            $table->longText('uraian_kegiatan')->nullable()->after('deskripsi');
            $table->decimal('latitude', 10, 8)->nullable()->after('foto');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            $table->dropColumn(['uraian_kegiatan', 'latitude', 'longitude']);
        });
    }
};
