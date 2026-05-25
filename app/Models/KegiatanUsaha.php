<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KegiatanUsaha extends Model
{
    protected $table = 'kegiatan_usaha';

    protected $fillable = [
        'nama_usaha', 'jenis_usaha', 'deskripsi',
        'tanggal_mulai', 'tanggal_selesai', 'status', 'created_by',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function modal(): HasMany
    {
        return $this->hasMany(UsahaModal::class)->orderBy('tanggal_terima');
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(UsahaTransaksi::class)->orderByDesc('tanggal');
    }

    public function harian(): HasMany
    {
        return $this->hasMany(UsahaHarian::class)->orderByDesc('tanggal');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
