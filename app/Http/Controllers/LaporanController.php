<?php

namespace App\Http\Controllers;

use App\Models\Output;
use App\Models\Akumulatif;
use App\Models\Capaian;
use App\Exports\LaporanExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index()
    {
        $tahun = session('tahun');
        $outputs = Output::with(['akumulatif' => function($query) use ($tahun) {
            $query->where('tahun', $tahun);
        }])->where('tahun', $tahun)->orderBy('kode_output')->get();
        $capaian = Capaian::where('tahun', $tahun)->get();
        $totalAnggaran = $outputs->sum('anggaran');
        $totalRealisasi = $outputs->sum(fn($o) => $o->akumulatif?->anggaran_akumulatif ?? 0);
        return view('laporan.index', compact('outputs', 'capaian', 'totalAnggaran', 'totalRealisasi', 'tahun'));
    }

    public function cetak()
    {
        $tahun = session('tahun');
        $outputs = Output::with(['akumulatif' => function($query) use ($tahun) {
            $query->where('tahun', $tahun);
        }, 'realisasi' => function($query) use ($tahun) {
            $query->where('tahun', $tahun);
        }])->where('tahun', $tahun)->orderBy('kode_output')->get();
        $capaian = Capaian::with(['realisasiBulanan' => function($q) use ($tahun) {
            $q->where('tahun', $tahun);
        }])->where('tahun', $tahun)->get();
        $bulanList = \App\Models\Output::getBulanList();
        return view('laporan.cetak', compact('outputs', 'capaian', 'bulanList', 'tahun'));
    }

    public function exportExcel()
    {
        $tahun = session('tahun');
        return Excel::download(new LaporanExport($tahun), "laporan-emovie-{$tahun}.xlsx");
    }

    public function exportPdf()
    {
        $tahun = session('tahun');
        $outputs = Output::with(['akumulatif' => function($query) use ($tahun) {
            $query->where('tahun', $tahun);
        }, 'realisasi' => function($query) use ($tahun) {
            $query->where('tahun', $tahun);
        }])->where('tahun', $tahun)->orderBy('kode_output')->get();
        $capaian = Capaian::with(['realisasiBulanan' => function($q) use ($tahun) {
            $q->where('tahun', $tahun);
        }])->where('tahun', $tahun)->get();
        $bulanList = \App\Models\Output::getBulanList();
        $pdf = Pdf::loadView('laporan.cetak-pdf', compact('outputs', 'capaian', 'bulanList', 'tahun'))
            ->setPaper('a4', 'landscape');
        return $pdf->download("laporan-emovie-{$tahun}.pdf");
    }
}
