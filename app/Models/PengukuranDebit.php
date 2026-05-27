<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengukuranDebit extends Model
{
    protected $table = 'pengukuran_debit';

    protected $fillable = [
        'sumber_air_id', 'tanggal', 'debit', 'satuan',
        'nama_petugas', 'latitude', 'longitude', 'foto', 'catatan', 'created_by',
    ];

    protected $casts = [
        'tanggal'   => 'date',
        'debit'     => 'float',
        'latitude'  => 'float',
        'longitude' => 'float',
    ];

    public static array $satuanOptions = ['liter/detik', 'm3/hari'];

    public function sumberAir(): BelongsTo
    {
        return $this->belongsTo(SumberAir::class);
    }

    public function getDebitFormatAttribute(): string
    {
        return number_format($this->debit, 2, '.', '') . ' ' . $this->satuan;
    }
}
