<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bpjs extends Model
{
    protected $table = 'bpjs';

    protected $fillable = [
        'penyadap_id', 'jenis_bpjs', 'nomor', 'status_aktif', 'penanggung'
    ];

    public function penyadap()
    {
        return $this->belongsTo(Penyadap::class);
    }
}