<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsahaModal extends Model
{
    protected $table = 'usaha_modal';

    protected $fillable = [
        'kegiatan_usaha_id', 'sumber', 'jumlah', 'tanggal_terima', 'keterangan',
    ];

    protected $casts = [
        'tanggal_terima' => 'date',
        'jumlah'         => 'integer',
    ];

    public function usaha(): BelongsTo
    {
        return $this->belongsTo(KegiatanUsaha::class, 'kegiatan_usaha_id');
    }

    public static array $sumberLabels = [
        'hibah_pemerintah' => 'Hibah Pemerintah',
        'pinjaman_bank'    => 'Pinjaman Bank / KSP',
        'modal_sendiri'    => 'Modal Sendiri',
        'donasi_csr'       => 'Donasi / CSR',
        'lain_lain'        => 'Lain-lain',
    ];
}
