<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsahaHarianFoto extends Model
{
    protected $table = 'usaha_harian_foto';

    protected $fillable = ['usaha_harian_id', 'file_path', 'keterangan', 'urutan'];

    public function harian(): BelongsTo
    {
        return $this->belongsTo(UsahaHarian::class, 'usaha_harian_id');
    }
}
