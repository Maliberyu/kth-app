<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokInventaris extends Model
{
    protected $table = 'stok_inventaris';

    protected $fillable = ['inventaris_id', 'total_stok'];

    public function inventaris()
    {
        return $this->belongsTo(Inventaris::class);
    }
}