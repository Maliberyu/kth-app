<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    protected $table = 'periode';

    protected $fillable = ['nama_periode', 'tanggal_mulai', 'tanggal_selesai'];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class);
    }
}