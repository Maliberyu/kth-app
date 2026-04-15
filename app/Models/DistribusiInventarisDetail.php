<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistribusiInventarisDetail extends Model
{
    // karena sekarang sudah pakai default Laravel (plural)
    // tabel: distribusi_inventaris_details

    protected $fillable = [
        'distribusi_id',
        'inventaris_id',
        'jumlah',
    ];

    /**
     * Relasi ke Distribusi Inventaris
     */
    public function distribusi()
    {
        return $this->belongsTo(DistribusiInventaris::class, 'distribusi_id');
    }

    /**
     * Relasi ke Inventaris
     */
    public function inventaris()
    {
        return $this->belongsTo(Inventaris::class, 'inventaris_id');
    }
}