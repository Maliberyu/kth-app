<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenugasanBlok extends Model
{
    protected $table = 'penugasan_blok';

    protected $fillable = ['penyadap_id', 'blok_id'];

    public function penyadap()
    {
        return $this->belongsTo(Penyadap::class);
    }

    public function blok()
    {
        return $this->belongsTo(Blok::class);
    }
}