<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Kegiatan extends Model
{
    protected $fillable = [
        'nama_kegiatan', 'deskripsi', 'lokasi',
        'tanggal_mulai', 'tanggal_selesai',
        'tipe', 'status', 'qr_token', 'foto', 'created_by',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->qr_token)) {
                $model->qr_token = Str::random(32);
            }
        });
    }

    public function peserta(): HasMany
    {
        return $this->hasMany(KegiatanPeserta::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getRegistrasiUrlAttribute(): string
    {
        return url('/daftar/' . $this->qr_token);
    }
}
