<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KegiatanFoto extends Model
{
    protected $table = 'kegiatan_foto';

    protected $fillable = ['kegiatan_id', 'file_path', 'keterangan', 'urutan'];

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }
}
