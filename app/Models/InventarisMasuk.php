<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventarisMasuk extends Model
{
    protected $table = 'inventaris_masuk';

    protected $fillable = ['vendor_id', 'kth_id', 'tanggal', 'keterangan'];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function kth()
    {
        return $this->belongsTo(Kth::class);
    }

    public function detail()
    {
        return $this->hasMany(InventarisMasukDetail::class);
    }
}