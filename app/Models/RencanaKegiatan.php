<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RencanaKegiatan extends Model
{
    protected $table    = 'rencana_kegiatan';
    protected $fillable = [];

    public function links()
    {
        return $this->hasMany(RencanaKegiatanLink::class, 'wilayah_id');
    }
}
