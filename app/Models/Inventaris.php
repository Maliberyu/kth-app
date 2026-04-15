<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventaris extends Model
{
    protected $table = 'inventaris';

    protected $fillable = ['kth_id', 'nama_barang', 'satuan'];

    public function kth()
    {
        return $this->belongsTo(Kth::class);
    }

    public function stok()
    {
        return $this->hasOne(StokInventaris::class);
    }

    public function masukDetail()
    {
        return $this->hasMany(InventarisMasukDetail::class);
    }

    public function distribusiDetail()
    {
        return $this->hasMany(DistribusiInventarisDetail::class);
    }
}