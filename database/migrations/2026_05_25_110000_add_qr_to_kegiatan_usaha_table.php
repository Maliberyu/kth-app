<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kegiatan_usaha', function (Blueprint $table) {
            $table->string('qr_token', 32)->nullable()->unique()->after('created_by');
            $table->boolean('qr_aktif')->default(true)->after('qr_token');
        });
    }

    public function down(): void
    {
        Schema::table('kegiatan_usaha', function (Blueprint $table) {
            $table->dropColumn(['qr_token', 'qr_aktif']);
        });
    }
};
