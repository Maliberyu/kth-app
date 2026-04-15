<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $table = 'vendor';

    protected $fillable = ['nama_vendor', 'no_hp', 'alamat'];

    public function suratJalan()
    {
        return $this->hasMany(SuratJalan::class);
    }

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class);
    }

    public function inventarisMasuk()
    {
        return $this->hasMany(InventarisMasuk::class);
    }
}