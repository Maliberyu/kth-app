<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsahaTransaksi extends Model
{
    protected $table = 'usaha_transaksi';

    protected $fillable = [
        'kegiatan_usaha_id', 'tipe', 'kategori', 'jumlah', 'tanggal', 'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah'  => 'integer',
    ];

    public function usaha(): BelongsTo
    {
        return $this->belongsTo(KegiatanUsaha::class, 'kegiatan_usaha_id');
    }

    public static array $kategoriPemasukan = [
        'penjualan'       => 'Penjualan Produk',
        'pendapatan_jasa' => 'Pendapatan Jasa',
        'lain_lain'       => 'Lain-lain',
    ];

    public static array $kategoriPengeluaran = [
        'bahan_baku'   => 'Bahan Baku',
        'operasional'  => 'Biaya Operasional',
        'tenaga_kerja' => 'Tenaga Kerja',
        'transportasi' => 'Transportasi',
        'lain_lain'    => 'Lain-lain',
    ];
}
