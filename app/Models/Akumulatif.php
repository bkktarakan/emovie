<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Akumulatif extends Model
{
    protected $table = 'akumulatif';

    protected $fillable = [
        'output_id', 'tahun', 'kode_output',
        'volume_akumulatif', 'persentase_volume',
        'anggaran_akumulatif', 'persentase_anggaran',
        'status', 'pcro',
    ];

    protected $casts = [
        'volume_akumulatif' => 'integer',
        'anggaran_akumulatif' => 'integer',
        'persentase_volume' => 'double',
        'persentase_anggaran' => 'double',
        'pcro' => 'double',
        'tahun' => 'integer',
    ];

    public function output(): BelongsTo
    {
        return $this->belongsTo(Output::class, 'output_id');
    }
}
