<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Komoditas extends Model
{
    protected $table = 'komoditas';

    protected $fillable = ['kups_id', 'nama_komoditas', 'volume', 'satuan', 'harga', 'keterangan'];

    protected $casts = [
        'harga'  => 'integer',
        'volume' => 'float',
    ];

    public static array $satuanOptions = ['Kg', 'Ikat', 'Batang', 'Liter', 'Buah'];

    public function kups(): BelongsTo
    {
        return $this->belongsTo(Kups::class);
    }

    public function getHargaFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    public function getVolumeFormatAttribute(): string
    {
        $v = $this->volume;
        return ($v == intval($v)) ? number_format(intval($v), 0, ',', '.') : number_format($v, 2, ',', '.');
    }
}
