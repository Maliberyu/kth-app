<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventarisMasukDetail extends Model
{
    protected $table = 'inventaris_masuk_detail';

    protected $fillable = ['inventaris_masuk_id', 'inventaris_id', 'jumlah'];

    public function inventarisMasuk()
    {
        return $this->belongsTo(InventarisMasuk::class);
    }

    public function inventaris()
    {
        return $this->belongsTo(Inventaris::class);
    }
}