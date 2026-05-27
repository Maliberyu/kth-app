<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KampungLayanan extends Model
{
    protected $table = 'kampung_layanan';

    protected $fillable = [
        'bak_tampung_id', 'nama_kampung', 'jumlah_kk',
    ];

    public function bakTampung(): BelongsTo
    {
        return $this->belongsTo(BakTampung::class);
    }
}
