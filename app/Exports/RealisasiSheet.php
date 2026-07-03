<?php

namespace App\Exports;

use App\Models\Output;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RealisasiSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(private int $tahun) {}

    public function title(): string { return 'Realisasi Output'; }

    public function headings(): array
    {
        return ['No', 'Kode Output', 'Nama Output', 'Volume Target', 'Satuan',
                'Pagu Anggaran (Rp)', 'Vol. Realisasi', '% Volume',
                'Anggaran Realisasi (Rp)', '% Anggaran', 'PCRO', 'Status'];
    }

    public function collection()
    {
        $rows = collect();
        $outputs = Output::with('akumulatif')->where('tahun', $this->tahun)->orderBy('kode_output')->get();
        foreach ($outputs as $i => $o) {
            $akm = $o->akumulatif;
            $rows->push([
                $i + 1,
                $o->kode_output,
                $o->nama_output,
                $o->volume,
                $o->satuan,
                $o->anggaran,
                $akm?->volume_akumulatif ?? 0,
                round($akm?->persentase_volume ?? 0, 2),
                $akm?->anggaran_akumulatif ?? 0,
                round($akm?->persentase_anggaran ?? 0, 2),
                round($akm?->pcro ?? 0, 2),
                $akm?->status ?? 'Belum Mulai',
            ]);
        }
        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E40AF']], 'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']]],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 5, 'B' => 20, 'C' => 45, 'D' => 14, 'E' => 10, 'F' => 20,
                'G' => 14, 'H' => 10, 'I' => 22, 'J' => 10, 'K' => 8, 'L' => 16];
    }
}
