<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Komponen extends Model
{
    protected $table = 'komponen';

    protected $fillable = ['sub_menu', 'komponen', 'tahun'];

    protected $casts = ['sub_menu' => 'integer', 'tahun' => 'integer'];

    public function subkomponen(): HasMany
    {
        return $this->hasMany(Subkomponen::class, 'komponen_id');
    }

    public static function getSubMenuLabel(int $subMenu): string
    {
        return match ($subMenu) {
            1 => 'Perencanaan Kinerja',
            2 => 'Pengukuran Kinerja',
            3 => 'Pelaporan Kinerja',
            4 => 'Evaluasi Kinerja',
            default => 'Tidak Diketahui',
        };
    }
}
