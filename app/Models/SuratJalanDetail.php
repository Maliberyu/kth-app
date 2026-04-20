// app/Models/SuratJalanDetail.php

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratJalanDetail extends Model
{
    protected $table = 'surat_jalan_details';

    protected $fillable = [
        'surat_jalan_id', 'produksi_getah_id', 'berat'
    ];

    // 🔥 Relasi ke SuratJalan
    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class, 'surat_jalan_id');
    }

    // 🔥 Relasi ke ProduksiGetah (untuk ambil data penyadap & blok)
    public function produksiGetah()
    {
        return $this->belongsTo(ProduksiGetah::class, 'produksi_getah_id');
    }
}