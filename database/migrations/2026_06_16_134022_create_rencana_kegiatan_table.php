<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rencana_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('urutan');
            $table->string('nama_wilayah', 200);
            $table->string('link', 500)->nullable();
            $table->timestamps();
        });

        DB::table('rencana_kegiatan')->insert([
            ['urutan' => 1, 'nama_wilayah' => 'Kantor Induk',                'link' => null, 'created_at' => now(), 'updated_at' => now()],
            ['urutan' => 2, 'nama_wilayah' => 'Wilayah Kerja Tanjung Selor', 'link' => null, 'created_at' => now(), 'updated_at' => now()],
            ['urutan' => 3, 'nama_wilayah' => 'Wilayah Kerja Berau',         'link' => null, 'created_at' => now(), 'updated_at' => now()],
            ['urutan' => 4, 'nama_wilayah' => 'Wilayah Kerja Bunyu',         'link' => null, 'created_at' => now(), 'updated_at' => now()],
            ['urutan' => 5, 'nama_wilayah' => 'Wilayah Kerja Nunukan',       'link' => null, 'created_at' => now(), 'updated_at' => now()],
            ['urutan' => 6, 'nama_wilayah' => 'Wilayah Kerja Sebatik',       'link' => null, 'created_at' => now(), 'updated_at' => now()],
            ['urutan' => 7, 'nama_wilayah' => 'Pos Kesehatan Malinau',       'link' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('rencana_kegiatan');
    }
};
