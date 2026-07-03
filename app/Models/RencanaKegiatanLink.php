<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RencanaKegiatanLink extends Model
{
    protected $table    = 'rencana_kegiatan_links';
    protected $fillable = ['wilayah_id', 'tahun', 'link'];
}
