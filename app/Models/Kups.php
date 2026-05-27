<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kups extends Model
{
    protected $table = 'kups';

    protected $fillable = ['nama_kups', 'deskripsi', 'status', 'created_by'];

    public function komoditas(): HasMany
    {
        return $this->hasMany(Komoditas::class)->orderBy('nama_komoditas');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
