<?php

namespace App\Exports;

use App\Models\Output;
use App\Models\Capaian;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LaporanExport implements WithMultipleSheets
{
    public function __construct(private int $tahun) {}

    public function sheets(): array
    {
        return [
            new RealisasiSheet($this->tahun),
            new CapaianSheet($this->tahun),
        ];
    }
}
