<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kups', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kups');
            $table->text('deskripsi')->nullable();
            $table->string('status')->default('aktif');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kups');
    }
};
