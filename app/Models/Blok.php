<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blok extends Model
{
    protected $table = 'blok';

    protected $fillable = [
        'kth_id', 'nama_blok', 'jenis_blok', 'luas',
        'total_pohon', 'pohon_produktif', 'pohon_tidak_produktif'
    ];

    public function kth()
    {
        return $this->belongsTo(Kth::class);
    }

    public function blokPeta()
    {
        return $this->hasMany(BlokPeta::class);
    }

    public function penugasanBlok()
    {
        return $this->hasMany(PenugasanBlok::class);
    }

    public function penyadap()
    {
        return $this->belongsToMany(Penyadap::class, 'penugasan_blok');
    }

    public function produksiGetah()
    {
        return $this->hasMany(ProduksiGetah::class);
    }

    public function penjualanDetail()
    {
        return $this->hasMany(PenjualanDetail::class);
    }
}