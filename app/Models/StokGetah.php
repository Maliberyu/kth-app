<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokGetah extends Model
{
    protected $table = 'stok_getah';

    protected $fillable = ['penyimpanan_id', 'total_stok'];

    public function penyimpanan()
    {
        return $this->belongsTo(Penyimpanan::class);
    }
}