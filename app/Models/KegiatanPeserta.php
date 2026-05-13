<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KegiatanPeserta extends Model
{
    protected $table = 'kegiatan_peserta';

    protected $fillable = [
        'kegiatan_id', 'nama_lengkap', 'jabatan',
        'asal_instansi', 'email', 'no_hp', 'waktu_hadir',
    ];

    protected $casts = [
        'waktu_hadir' => 'datetime',
    ];

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }
}
