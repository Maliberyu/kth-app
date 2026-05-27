<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LahanLayanan extends Model
{
    protected $table = 'lahan_layanan';

    protected $fillable = [
        'sumber_air_id', 'nama_lahan', 'tipe_lahan', 'luas_ha',
    ];

    protected $casts = [
        'luas_ha' => 'float',
    ];

    public static array $tipeOptions = ['sawah', 'kolam', 'kebun'];

    public function sumberAir(): BelongsTo
    {
        return $this->belongsTo(SumberAir::class);
    }
}
