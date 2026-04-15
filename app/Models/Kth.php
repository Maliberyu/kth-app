<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough; 

class Kth extends Model
{
    protected $table = 'kth';

    protected $fillable = ['nama_kth', 'alamat'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function penyadap()
    {
        return $this->hasMany(Penyadap::class);
    }

    public function blok()
    {
        return $this->hasMany(Blok::class);
    }

    public function penyimpanan()
    {
        return $this->hasMany(Penyimpanan::class);
    }

    public function inventaris()
    {
        return $this->hasMany(Inventaris::class);
    }
    // Tambahkan method ini di class Kth
    public function produksiGetah(): HasManyThrough
    {
        return $this->hasManyThrough(
            ProduksiGetah::class,
            Penyadap::class,
            'kth_id',
            'penyadap_id',
            'id',
            'id'
        );
    }
}