<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SumberAir extends Model
{
    protected $table = 'sumber_air';

    protected $fillable = [
        'nama', 'tipe', 'deskripsi', 'latitude', 'longitude', 'status', 'created_by',
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
    ];

    public function bakTampung(): HasOne
    {
        return $this->hasOne(BakTampung::class);
    }

    public function lahanLayanan(): HasMany
    {
        return $this->hasMany(LahanLayanan::class);
    }

    public function pengukuran(): HasMany
    {
        return $this->hasMany(PengukuranDebit::class)->orderBy('tanggal', 'desc');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getDebitTerakhirAttribute(): ?PengukuranDebit
    {
        return $this->pengukuran()->first();
    }

    public function getTrendAttribute(): string
    {
        $last = $this->pengukuran()->take(2)->get();
        if ($last->count() < 2) return 'stabil';
        return $last[0]->debit > $last[1]->debit ? 'naik'
            : ($last[0]->debit < $last[1]->debit ? 'turun' : 'stabil');
    }

    public function getTipeLabelAttribute(): string
    {
        return $this->tipe === 'air_bersih' ? 'Air Bersih' : 'Air Baku';
    }
}
