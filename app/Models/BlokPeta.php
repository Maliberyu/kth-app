<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlokPeta extends Model
{
    protected $table = 'blok_peta';

    protected $fillable = [
        'blok_id', 'dibuat_oleh', 'geojson', 'status_mapping', 'catatan'
    ];

    public function blok()
    {
        return $this->belongsTo(Blok::class);
    }

    public function dibuatOleh()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}