<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistribusiInventaris extends Model
{
    protected $table = 'distribusi_inventaris';

    protected $fillable = ['penyadap_id', 'kth_id', 'tanggal', 'keterangan'];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function penyadap()
    {
        return $this->belongsTo(Penyadap::class);
    }

    public function kth()
    {
        return $this->belongsTo(Kth::class);
    }

    public function detail()
    {
        return $this->hasMany(DistribusiInventarisDetail::class);
    }
    // public function details()
    // {
    //     return $this->hasMany(DistribusiInventarisDetail::class, 'distribusi_id');
    // }
}