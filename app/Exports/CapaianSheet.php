<?php

namespace App\Exports;

use App\Models\Capaian;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CapaianSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(private int $tahun) {}

    public function title(): string { return 'Indikator Kinerja'; }

    public function headings(): array
    {
        return ['No', 'Indikator Kinerja', 'Target', 'Realisasi', '% Capaian', 'Link Data Dukung'];
    }

    public function collection()
    {
        $rows = collect();
        foreach (Capaian::where('tahun', $this->tahun)->get() as $i => $c) {
            $rows->push([$i + 1, $c->indikator, $c->target, $c->realisasi, round($c->persentase, 2), $c->link ?? '-']);
        }
        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '065F46']]],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 5, 'B' => 60, 'C' => 12, 'D' => 12, 'E' => 12, 'F' => 40];
    }
}
