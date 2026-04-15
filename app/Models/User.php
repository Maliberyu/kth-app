<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $fillable = [
        'kth_id', 'nama', 'username', 'email', 'password', 'role', 'penyadap_id'
    ];

    protected $hidden = ['password', 'remember_token'];

    public function kth()
    {
        return $this->belongsTo(Kth::class);
    }

    public function penyadap()
    {
        return $this->belongsTo(Penyadap::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}