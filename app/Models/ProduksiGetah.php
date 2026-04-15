<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduksiGetah extends Model
{
    protected $table = 'produksi_getah';

    protected $fillable = [
        'penyadap_id', 'blok_id', 'penyimpanan_id',
        'tanggal', 'berat', 'diinput_oleh', 'status_validasi', 'catatan'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function penyadap()
    {
        return $this->belongsTo(Penyadap::class);
    }

    public function blok()
    {
        return $this->belongsTo(Blok::class);
    }

    public function penyimpanan()
    {
        return $this->belongsTo(Penyimpanan::class);
    }

    public function diinputOleh()
    {
        return $this->belongsTo(User::class, 'diinput_oleh');
    }
}