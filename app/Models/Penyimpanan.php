<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penyimpanan extends Model
{
    protected $table = 'penyimpanan';

    protected $fillable = ['kth_id', 'nama_lokasi'];

    public function kth()
    {
        return $this->belongsTo(Kth::class);
    }

    public function produksiGetah()
    {
        return $this->hasMany(ProduksiGetah::class);
    }

    public function stokGetah()
    {
        return $this->hasOne(StokGetah::class);
    }

    public function suratJalan()
    {
        return $this->hasMany(SuratJalan::class);
    }
}