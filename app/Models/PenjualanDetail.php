<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenjualanDetail extends Model
{
    protected $table = 'penjualan_detail';

    protected $fillable = [
        'penjualan_id', 'penyadap_id', 'blok_id',
        'berat', 'harga_beli', 'total_beli'
    ];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function penyadap()
    {
        return $this->belongsTo(Penyadap::class);
    }

    public function blok()
    {
        return $this->belongsTo(Blok::class);
    }
}