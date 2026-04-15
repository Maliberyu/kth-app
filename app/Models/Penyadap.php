<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penyadap extends Model
{
    protected $table = 'penyadap';

    protected $fillable = ['kth_id', 'nama', 'nik', 'no_hp', 'alamat'];

    public function kth()
    {
        return $this->belongsTo(Kth::class);
    }

    public function bpjs()
    {
        return $this->hasMany(Bpjs::class);
    }

    public function penugasanBlok()
    {
        return $this->hasMany(PenugasanBlok::class);
    }

    public function blok()
    {
        return $this->belongsToMany(Blok::class, 'penugasan_blok');
    }

    public function produksiGetah()
    {
        return $this->hasMany(ProduksiGetah::class);
    }

    public function penjualanDetail()
    {
        return $this->hasMany(PenjualanDetail::class);
    }

    public function distribusiInventaris()
    {
        return $this->hasMany(DistribusiInventaris::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }
}