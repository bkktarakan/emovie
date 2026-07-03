<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MigrasiDataLama extends Command
{
    protected $signature   = 'emovie:migrasi-data {--force : Jalankan tanpa konfirmasi}';
    protected $description = 'Migrasi data dari db_emovie (lama) ke db_emovie_laravel (baru)';

    private array $mapOutput    = []; // old string id → new int id
    private array $mapCapaian   = []; // old string id → new int id
    private array $mapKomponen  = []; // old string id → new int id

    private array $bulanUrutan = [
        'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
        'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
        'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12,
    ];

    public function handle(): int
    {
        $this->info('=== Migrasi Data eMovie ===');
        $this->warn('Proses ini akan MENGHAPUS data lama di db_emovie_laravel dan menggantinya dengan data dari db_emovie.');
        $this->newLine();

        if (!$this->option('force') && !$this->confirm('Lanjutkan migrasi?', false)) {
            $this->line('Dibatalkan.');
            return 1;
        }

        $old = DB::connection('old');

        // Hapus data lama (urutan dari child ke parent)
        $this->line('Membersihkan data lama...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('realisasi_bulanan')->truncate();
        DB::table('subkomponen')->truncate();
        DB::table('komponen')->truncate();
        DB::table('realisasi')->truncate();
        DB::table('akumulatif')->truncate();
        DB::table('capaian')->truncate();
        DB::table('outputs')->truncate();
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->info('✓ Data lama dibersihkan');

        $this->migrasiUser($old);
        $this->migrasiOutput($old);
        $this->migrasiAkumulatif($old);
        $this->migrasiRealisasi($old);
        $this->migrasiCapaian($old);
        $this->migrasiRealisasiBulanan($old);
        $this->migrasiKomponen($old);
        $this->migrasiSubkomponen($old);

        $this->newLine();
        $this->info('=== Migrasi Selesai! ===');
        $this->table(
            ['Tabel', 'Jumlah Record'],
            [
                ['users',             DB::table('users')->count()],
                ['outputs',           DB::table('outputs')->count()],
                ['akumulatif',        DB::table('akumulatif')->count()],
                ['realisasi',         DB::table('realisasi')->count()],
                ['capaian',           DB::table('capaian')->count()],
                ['realisasi_bulanan', DB::table('realisasi_bulanan')->count()],
                ['komponen',          DB::table('komponen')->count()],
                ['subkomponen',       DB::table('subkomponen')->count()],
            ]
        );

        return 0;
    }

    private function migrasiUser($old): void
    {
        $this->line('Migrasi users...');
        $users = $old->table('tbl_user')->get();
        $now   = now();
        $count = 0;

        foreach ($users as $u) {
            // Password lama disimpan plain text → hash dengan bcrypt
            // Deteksi apakah sudah di-hash (panjang bcrypt = 60 karakter dimulai $2y$)
            $password = (strlen($u->password) === 60 && str_starts_with($u->password, '$2y$'))
                ? $u->password
                : Hash::make($u->password);

            DB::table('users')->insert([
                'name'       => ucfirst($u->username),
                'username'   => $u->username,
                'email'      => $u->username . '@emovie.go.id',
                'password'   => $password,
                'level'      => $u->level === 'admin' ? 'admin' : 'operator',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $count++;
        }
        $this->info("  ✓ {$count} user dimigrasikan");
    }

    private function migrasiOutput($old): void
    {
        $this->line('Migrasi outputs...');
        $outputs = $old->table('tbl_output')->orderBy('id')->get();
        $now     = now();
        $count   = 0;

        foreach ($outputs as $o) {
            $newId = DB::table('outputs')->insertGetId([
                'tahun'       => $o->tahun,
                'kode_output' => $o->kodeOutput,
                'nama_output' => $o->namaOutput,
                'volume'      => $o->volume,
                'satuan'      => $o->satuan,
                'anggaran'    => $o->anggaran,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
            $this->mapOutput[$o->id] = $newId;
            $count++;
        }
        $this->info("  ✓ {$count} output dimigrasikan");
    }

    private function migrasiAkumulatif($old): void
    {
        $this->line('Migrasi akumulatif...');
        $rows  = $old->table('tbl_akumulatif')->get();
        $now   = now();
        $count = 0;
        $skip  = 0;

        // Buat mapping kodeOutput → output_id
        $kodeToOutputId = DB::table('outputs')->pluck('id', 'kode_output');

        foreach ($rows as $r) {
            $outputId = $kodeToOutputId[$r->kodeOutput] ?? null;
            if (!$outputId) {
                $skip++;
                continue;
            }

            DB::table('akumulatif')->insert([
                'output_id'           => $outputId,
                'tahun'               => $r->tahun,
                'kode_output'         => $r->kodeOutput,
                'volume_akumulatif'   => $r->volumeAkumulatif,
                'persentase_volume'   => $r->persentaseVolume,
                'anggaran_akumulatif' => $r->anggaranAkumulatif,
                'persentase_anggaran' => $r->persentaseAnggaran,
                'status'              => $r->status ?? '',
                'pcro'                => $r->pcro,
                'created_at'          => $now,
                'updated_at'          => $now,
            ]);
            $count++;
        }
        $this->info("  ✓ {$count} akumulatif dimigrasikan" . ($skip ? ", {$skip} dilewati (output tidak ditemukan)" : ''));
    }

    private function migrasiRealisasi($old): void
    {
        $this->line('Migrasi realisasi (1788 rows)...');
        $rows  = $old->table('tbl_realisasi')->orderBy('id')->get();
        $now   = now();
        $count = 0;
        $skip  = 0;

        foreach ($rows as $r) {
            $outputId = $this->mapOutput[$r->idOutput] ?? null;
            if (!$outputId) {
                $skip++;
                continue;
            }

            $urutan = $this->bulanUrutan[$r->bulan] ?? 0;

            DB::table('realisasi')->insert([
                'output_id'           => $outputId,
                'tahun'               => $r->tahun,
                'kode_output'         => $r->kodeOutput,
                'bulan'               => $r->bulan,
                'urutan_bulan'        => $urutan,
                'realisasi_volume'    => $r->realisasiVolume,
                'persentase_volume'   => $r->persentaseVolume,
                'realisasi_anggaran'  => $r->realisasiAnggaran,
                'persentase_anggaran' => $r->persentaseAnggaran,
                'pcro'                => $r->pcro,
                'kemanfaatan'         => $r->kemanfaatan ?? '',
                'keterangan'          => $r->keterangan ?? '',
                'created_at'          => $now,
                'updated_at'          => $now,
            ]);
            $count++;
        }
        $this->info("  ✓ {$count} realisasi dimigrasikan" . ($skip ? ", {$skip} dilewati" : ''));
    }

    private function migrasiCapaian($old): void
    {
        $this->line('Migrasi capaian...');
        $rows  = $old->table('tbl_capaian')->orderBy('id')->get();
        $now   = now();
        $count = 0;

        foreach ($rows as $r) {
            $newId = DB::table('capaian')->insertGetId([
                'tahun'      => $r->tahun,
                'indikator'  => $r->indikator,
                'target'     => $r->target,
                'realisasi'  => $r->realisasi,
                'persentase' => $r->persentase,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $this->mapCapaian[$r->id] = $newId;
            $count++;
        }
        $this->info("  ✓ {$count} capaian dimigrasikan");
    }

    private function migrasiRealisasiBulanan($old): void
    {
        $this->line('Migrasi realisasi_bulanan...');
        $rows  = $old->table('tbl_realisasibulanan')->orderBy('idrealisasi')->get();
        $now   = now();
        $count = 0;
        $skip  = 0;

        foreach ($rows as $r) {
            $capaianId = $this->mapCapaian[$r->idcapaian] ?? null;
            if (!$capaianId) {
                $skip++;
                continue;
            }

            $urutan = $this->bulanUrutan[$r->bulan] ?? 0;

            DB::table('realisasi_bulanan')->insert([
                'capaian_id'   => $capaianId,
                'tahun'        => $r->tahun,
                'bulan'        => $r->bulan,
                'urutan_bulan' => $urutan,
                'realisasi'    => $r->realisasi1,
                'persentase'   => $r->persentase1,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
            $count++;
        }
        $this->info("  ✓ {$count} realisasi_bulanan dimigrasikan" . ($skip ? ", {$skip} dilewati" : ''));
    }

    private function migrasiKomponen($old): void
    {
        $this->line('Migrasi komponen...');
        $rows  = $old->table('tbl_komponen')->orderBy('id_komponen')->get();
        $now   = now();
        $count = 0;

        foreach ($rows as $r) {
            $subMenu = is_numeric($r->sub_menu) ? (int) $r->sub_menu : 1;
            $newId = DB::table('komponen')->insertGetId([
                'sub_menu'   => $subMenu,
                'komponen'   => $r->komponen,
                'tahun'      => $r->tahun,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $this->mapKomponen[$r->id_komponen] = $newId;
            $count++;
        }
        $this->info("  ✓ {$count} komponen dimigrasikan");
    }

    private function migrasiSubkomponen($old): void
    {
        $this->line('Migrasi subkomponen...');
        $rows  = $old->table('tbl_subkomponen')->orderBy('id_subkomponen')->get();
        $now   = now();
        $count = 0;
        $skip  = 0;

        foreach ($rows as $r) {
            $komponenId = $this->mapKomponen[$r->id_komponen] ?? null;
            if (!$komponenId) {
                $skip++;
                continue;
            }

            DB::table('subkomponen')->insert([
                'komponen_id' => $komponenId,
                'dakung'      => $r->dakung,
                'link'        => $r->link ?: null,
                'tahun'       => $r->tahun,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
            $count++;
        }
        $this->info("  ✓ {$count} subkomponen dimigrasikan" . ($skip ? ", {$skip} dilewati" : ''));
    }
}
