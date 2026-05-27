<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BakTampung extends Model
{
    protected $table = 'bak_tampung';

    protected $fillable = [
        'sumber_air_id', 'nama', 'kapasitas', 'latitude', 'longitude',
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
        'kapasitas' => 'float',
    ];

    public function sumberAir(): BelongsTo
    {
        return $this->belongsTo(SumberAir::class);
    }

    public function kampungLayanan(): HasMany
    {
        return $this->hasMany(KampungLayanan::class)->orderBy('nama_kampung');
    }
}
