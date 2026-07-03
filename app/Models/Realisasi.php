<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Realisasi extends Model
{
    protected $table = 'realisasi';

    protected $fillable = [
        'output_id', 'tahun', 'kode_output', 'bulan', 'urutan_bulan',
        'realisasi_volume', 'persentase_volume',
        'realisasi_anggaran', 'persentase_anggaran',
        'pcro', 'kemanfaatan', 'keterangan',
    ];

    protected $casts = [
        'realisasi_volume' => 'integer',
        'realisasi_anggaran' => 'integer',
        'persentase_volume' => 'double',
        'persentase_anggaran' => 'double',
        'pcro' => 'double',
        'tahun' => 'integer',
        'urutan_bulan' => 'integer',
    ];

    public function output(): BelongsTo
    {
        return $this->belongsTo(Output::class, 'output_id');
    }
}
