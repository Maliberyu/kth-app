<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualan';

    protected $fillable = [
        'surat_jalan_id', 'vendor_id', 'periode_id',
        'total_berat', 'harga_jual', 'total_penjualan'
    ];

    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class);
    }

    public function detail()
    {
        return $this->hasMany(PenjualanDetail::class);
    }
}