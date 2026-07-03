<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subkomponen extends Model
{
    protected $table = 'subkomponen';

    protected $fillable = ['komponen_id', 'dakung', 'link', 'tahun'];

    protected $casts = ['tahun' => 'integer'];

    public function komponen(): BelongsTo
    {
        return $this->belongsTo(Komponen::class, 'komponen_id');
    }
}
