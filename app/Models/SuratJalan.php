<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratJalan extends Model
{
    protected $table = 'surat_jalan';

    protected $fillable = [
        'nomor', 'tanggal', 'penyimpanan_id',
        'vendor_id', 'total_berat', 'status', 'keterangan'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function penyimpanan()
    {
        return $this->belongsTo(Penyimpanan::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function pengirimanGetah()
    {
        return $this->hasMany(PengirimanGetah::class);
    }

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class);
    }
}