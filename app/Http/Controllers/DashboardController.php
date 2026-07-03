<?php

namespace App\Http\Controllers;

use App\Models\Output;
use App\Models\Realisasi;
use App\Models\Akumulatif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $tahun = session('tahun');

        $totalOutput = Output::where('tahun', $tahun)->count();
        $totalAnggaran = Output::where('tahun', $tahun)->sum('anggaran');
        $totalRealisasiAnggaran = Akumulatif::where('tahun', $tahun)->sum('anggaran_akumulatif');
        $totalRealisasiVolume = Akumulatif::where('tahun', $tahun)->sum('volume_akumulatif');
        $totalVolume = Output::where('tahun', $tahun)->sum('volume');

        $pctAnggaran = $totalAnggaran > 0 ? round($totalRealisasiAnggaran / $totalAnggaran * 100, 2) : 0;
        $pctVolume = $totalVolume > 0 ? round($totalRealisasiVolume / $totalVolume * 100, 2) : 0;

        $bulanList = Output::getBulanList();
        $chartOutput = [];
        $chartAnggaran = [];

        $chartData = Realisasi::where('tahun', $tahun)
            ->selectRaw('urutan_bulan, SUM(realisasi_volume) as sum_volume, SUM(realisasi_anggaran) as sum_anggaran')
            ->groupBy('urutan_bulan')
            ->get()
            ->keyBy('urutan_bulan');

        $cumulativeVol = 0;
        $cumulativeAng = 0;
        foreach ($bulanList as $urutan => $bulanNama) {
            $cumulativeVol += (float) ($chartData[$urutan]->sum_volume   ?? 0);
            $cumulativeAng += (float) ($chartData[$urutan]->sum_anggaran ?? 0);
            $chartOutput[]   = $totalVolume   > 0 ? round($cumulativeVol / $totalVolume   * 100, 2) : 0;
            $chartAnggaran[] = $totalAnggaran > 0 ? round($cumulativeAng / $totalAnggaran * 100, 2) : 0;
        }

        $realisasiTerbaru = Akumulatif::with('output')
            ->where('tahun', $tahun)
            ->orderByDesc('persentase_volume')
            ->take(5)
            ->get();

        $perluPerhatian = Akumulatif::with('output')
            ->where('tahun', $tahun)
            ->where('status', 'Perlu Perhatian')
            ->orderBy('persentase_volume')
            ->get();

        return view('dashboard.index', compact(
            'tahun', 'totalOutput', 'totalAnggaran', 'totalRealisasiAnggaran',
            'pctAnggaran', 'pctVolume', 'chartOutput', 'chartAnggaran',
            'realisasiTerbaru', 'bulanList', 'perluPerhatian'
        ));
    }

    public function gantiTahun(Request $request)
    {
        $request->validate(['tahun' => ['required', 'integer', 'min:2020', 'max:2030']]);
        session(['tahun' => (int) $request->tahun]);
        return back()->with('success', 'Tahun anggaran diubah ke ' . $request->tahun);
    }
}
