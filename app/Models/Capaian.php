<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Capaian extends Model
{
    protected $table = 'capaian';

    protected $fillable = [
        'tahun', 'indikator', 'target', 'realisasi', 'persentase', 'link',
    ];

    protected $casts = [
        'target' => 'double',
        'realisasi' => 'double',
        'persentase' => 'double',
        'tahun' => 'integer',
    ];

    public function realisasiBulanan(): HasMany
    {
        return $this->hasMany(RealisasiBulanan::class, 'capaian_id')->orderBy('urutan_bulan');
    }
}
