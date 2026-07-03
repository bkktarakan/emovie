<?php

namespace App\Imports;

use App\Models\Output;
use App\Models\Realisasi;
use App\Models\Akumulatif;
use App\Models\ActivityLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class OutputImport implements ToCollection, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    public int $imported = 0;
    public int $skipped  = 0;
    private int $tahun;

    public function __construct(int $tahun)
    {
        $this->tahun = $tahun;
    }

    public function collection(Collection $rows)
    {
        $satuanValid = Output::getSatuanOptions();
        $bulanList   = Output::getBulanList();

        foreach ($rows as $row) {
            $kode    = strtoupper(trim($row['kode_output'] ?? ''));
            $nama    = trim($row['nama_output'] ?? '');
            $volume  = (int) ($row['volume'] ?? 0);
            $satuan  = trim($row['satuan'] ?? '');
            $anggaran = (int) ($row['anggaran'] ?? 0);

            if (!$kode || !$nama || $volume <= 0 || !in_array($satuan, $satuanValid)) {
                $this->skipped++;
                continue;
            }

            if (Output::where('kode_output', $kode)->where('tahun', $this->tahun)->exists()) {
                $this->skipped++;
                continue;
            }

            DB::transaction(function () use ($kode, $nama, $volume, $satuan, $anggaran, $bulanList) {
                $output = Output::create([
                    'tahun'       => $this->tahun,
                    'kode_output' => $kode,
                    'nama_output' => $nama,
                    'volume'      => $volume,
                    'satuan'      => $satuan,
                    'anggaran'    => $anggaran,
                ]);

                Akumulatif::create([
                    'output_id' => $output->id, 'tahun' => $this->tahun,
                    'kode_output' => $kode, 'volume_akumulatif' => 0,
                    'persentase_volume' => 0, 'anggaran_akumulatif' => 0,
                    'persentase_anggaran' => 0, 'status' => '', 'pcro' => 0,
                ]);

                foreach ($bulanList as $urutan => $bulan) {
                    Realisasi::create([
                        'output_id' => $output->id, 'tahun' => $this->tahun,
                        'kode_output' => $kode, 'bulan' => $bulan,
                        'urutan_bulan' => $urutan, 'realisasi_volume' => 0,
                        'persentase_volume' => 0, 'realisasi_anggaran' => 0,
                        'persentase_anggaran' => 0, 'pcro' => 0,
                        'kemanfaatan' => '', 'keterangan' => '',
                    ]);
                }
            });

            $this->imported++;
        }

        if ($this->imported > 0) {
            ActivityLog::record('Import', 'Output', "Import {$this->imported} output dari Excel (tahun {$this->tahun})");
        }
    }
}
