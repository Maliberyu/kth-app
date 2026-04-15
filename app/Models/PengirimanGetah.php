<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengirimanGetah extends Model
{
    protected $table = 'pengiriman_getah';

    protected $fillable = ['surat_jalan_id', 'jumlah_dikirim', 'catatan'];

    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class);
    }
}