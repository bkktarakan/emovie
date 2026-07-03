<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RealisasiBulanan extends Model
{
    protected $table = 'realisasi_bulanan';

    protected $fillable = [
        'capaian_id', 'tahun', 'bulan', 'urutan_bulan', 'realisasi', 'persentase',
    ];

    protected $casts = [
        'realisasi' => 'double',
        'persentase' => 'double',
        'tahun' => 'integer',
        'urutan_bulan' => 'integer',
    ];

    public function capaian(): BelongsTo
    {
        return $this->belongsTo(Capaian::class, 'capaian_id');
    }
}
