<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('realisasi_bulanan');
        Schema::dropIfExists('subkomponen');
        Schema::dropIfExists('komponen');
        Schema::dropIfExists('realisasi');
        Schema::dropIfExists('akumulatif');
        Schema::dropIfExists('capaian');
        Schema::dropIfExists('outputs');

        // Tabel users sudah dibuat oleh Laravel default migration
        // Tambahkan kolom level
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'level')) {
                $table->enum('level', ['admin', 'operator'])->default('operator')->after('email');
            }
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username', 50)->unique()->after('name');
            }
            if (Schema::hasColumn('users', 'email')) {
                $table->string('email')->nullable()->change();
            }
        });

        Schema::create('outputs', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->string('kode_output', 30);
            $table->index(['kode_output', 'tahun']);
            $table->text('nama_output');
            $table->integer('volume');
            $table->string('satuan', 30);
            $table->bigInteger('anggaran');
            $table->timestamps();
            $table->index('tahun');
        });

        Schema::create('akumulatif', function (Blueprint $table) {
            $table->id();
            $table->foreignId('output_id')->constrained('outputs')->cascadeOnDelete();
            $table->integer('tahun');
            $table->string('kode_output', 30);
            $table->integer('volume_akumulatif')->default(0);
            $table->double('persentase_volume')->default(0);
            $table->bigInteger('anggaran_akumulatif')->default(0);
            $table->double('persentase_anggaran')->default(0);
            $table->string('status')->nullable();
            $table->double('pcro')->default(0);
            $table->timestamps();
        });

        Schema::create('realisasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('output_id')->constrained('outputs')->cascadeOnDelete();
            $table->integer('tahun');
            $table->string('kode_output', 30);
            $table->string('bulan', 20);
            $table->integer('urutan_bulan')->default(0);
            $table->integer('realisasi_volume')->default(0);
            $table->double('persentase_volume')->default(0);
            $table->bigInteger('realisasi_anggaran')->default(0);
            $table->double('persentase_anggaran')->default(0);
            $table->double('pcro')->default(0);
            $table->text('kemanfaatan')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->index(['output_id', 'tahun', 'bulan']);
        });

        Schema::create('capaian', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->text('indikator');
            $table->double('target')->default(0);
            $table->double('realisasi')->default(0);
            $table->double('persentase')->default(0);
            $table->timestamps();
            $table->index('tahun');
        });

        Schema::create('realisasi_bulanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('capaian_id')->constrained('capaian')->cascadeOnDelete();
            $table->integer('tahun');
            $table->string('bulan', 20);
            $table->integer('urutan_bulan')->default(0);
            $table->double('realisasi')->default(0);
            $table->double('persentase')->default(0);
            $table->timestamps();
            $table->index(['capaian_id', 'tahun']);
        });

        Schema::create('komponen', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('sub_menu');
            $table->text('komponen');
            $table->integer('tahun');
            $table->timestamps();
            $table->index(['sub_menu', 'tahun']);
        });

        Schema::create('subkomponen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('komponen_id')->constrained('komponen')->cascadeOnDelete();
            $table->text('dakung');
            $table->string('link', 500)->nullable();
            $table->integer('tahun');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('realisasi_bulanan');
        Schema::dropIfExists('subkomponen');
        Schema::dropIfExists('komponen');
        Schema::dropIfExists('realisasi');
        Schema::dropIfExists('akumulatif');
        Schema::dropIfExists('capaian');
        Schema::dropIfExists('outputs');
    }
};
