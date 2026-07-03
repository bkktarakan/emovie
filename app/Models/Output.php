<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Output extends Model
{
    protected $table = 'outputs';

    protected $fillable = [
        'tahun', 'kode_output', 'nama_output', 'volume', 'satuan', 'anggaran',
    ];

    protected $casts = [
        'anggaran' => 'integer',
        'volume' => 'integer',
        'tahun' => 'integer',
    ];

    public function realisasi(): HasMany
    {
        return $this->hasMany(Realisasi::class, 'output_id')->orderBy('urutan_bulan');
    }

    public function akumulatif(): HasOne
    {
        return $this->hasOne(Akumulatif::class, 'output_id');
    }

    public static function getSatuanOptions(): array
    {
        return ['Bulan', 'Dokumen', 'Kegiatan', 'Laporan', 'Layanan', 'Orang', 'Paket', 'Unit'];
    }

    public static function getBulanList(): array
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
    }
}
