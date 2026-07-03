<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false;
    protected $table = 'activity_logs';
    protected $fillable = ['user_id', 'user_name', 'aksi', 'modul', 'deskripsi', 'created_at'];

    public static function record(string $aksi, string $modul, string $deskripsi): void
    {
        static::create([
            'user_id'   => session('user_id'),
            'user_name' => session('user_name'),
            'aksi'      => $aksi,
            'modul'     => $modul,
            'deskripsi' => $deskripsi,
            'created_at'=> now(),
        ]);
    }
}
