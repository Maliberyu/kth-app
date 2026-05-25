<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsahaHarian extends Model
{
    protected $table = 'usaha_harian';

    protected $fillable = [
        'kegiatan_usaha_id', 'tanggal', 'judul', 'isi', 'kondisi',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function fotos(): HasMany
    {
        return $this->hasMany(UsahaHarianFoto::class, 'usaha_harian_id')->orderBy('urutan');
    }

    public function usaha(): BelongsTo
    {
        return $this->belongsTo(KegiatanUsaha::class, 'kegiatan_usaha_id');
    }
}
