<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rencana_kegiatan_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wilayah_id');
            $table->unsignedSmallInteger('tahun');
            $table->string('link', 500)->nullable();
            $table->timestamps();

            $table->unique(['wilayah_id', 'tahun']);
            $table->foreign('wilayah_id')->references('id')->on('rencana_kegiatan')->onDelete('cascade');
        });

        // Migrasi data link lama ke tabel baru untuk tahun ini
        $tahun = (int) date('Y');
        foreach (DB::table('rencana_kegiatan')->whereNotNull('link')->get() as $row) {
            DB::table('rencana_kegiatan_links')->insert([
                'wilayah_id' => $row->id,
                'tahun'      => $tahun,
                'link'       => $row->link,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Hapus kolom link lama
        Schema::table('rencana_kegiatan', function (Blueprint $table) {
            $table->dropColumn('link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rencana_kegiatan_links');
    }
};
