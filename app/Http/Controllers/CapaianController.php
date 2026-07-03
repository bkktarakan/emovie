<?php

namespace App\Http\Controllers;

use App\Models\Capaian;
use App\Models\RealisasiBulanan;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CapaianController extends Controller
{
    public function index()
    {
        $tahun = session('tahun');
        $capaian = Capaian::with('realisasiBulanan')->where('tahun', $tahun)->get();
        $bulanList = \App\Models\Output::getBulanList();
        return view('capaian.index', compact('capaian', 'tahun', 'bulanList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'indikator' => ['required', 'string', 'max:500'],
            'target'    => ['required', 'numeric', 'min:0', 'max:9999'],
            'link'      => ['nullable', 'url', 'max:500'],
        ]);

        $tahun = session('tahun');
        $bulanList = \App\Models\Output::getBulanList();

        DB::transaction(function () use ($validated, $tahun, $bulanList) {
            $capaian = Capaian::create([
                'tahun'      => $tahun,
                'indikator'  => $validated['indikator'],
                'target'     => $validated['target'],
                'realisasi'  => 0,
                'persentase' => 0,
                'link'       => $validated['link'] ?? null,
            ]);

            foreach ($bulanList as $urutan => $bulan) {
                RealisasiBulanan::create([
                    'capaian_id'   => $capaian->id,
                    'tahun'        => $tahun,
                    'bulan'        => $bulan,
                    'urutan_bulan' => $urutan,
                    'realisasi'    => 0,
                    'persentase'   => 0,
                ]);
            }
        });

        ActivityLog::record('Tambah', 'Indikator Kinerja', "Menambahkan indikator: " . \Str::limit($validated['indikator'], 80));
        return redirect()->route('capaian.index')->with('success', 'Indikator kinerja berhasil ditambahkan.');
    }

    public function update(Request $request, Capaian $capaian)
    {
        $validated = $request->validate([
            'indikator' => ['required', 'string', 'max:500'],
            'target'    => ['required', 'numeric', 'min:0', 'max:9999'],
            'link'      => ['nullable', 'url', 'max:500'],
        ]);
        $capaian->update($validated);
        ActivityLog::record('Edit', 'Indikator Kinerja', "Memperbarui indikator: " . \Str::limit($validated['indikator'], 80));
        return redirect()->route('capaian.index')->with('success', 'Indikator berhasil diperbarui.');
    }

    public function destroy(Capaian $capaian)
    {
        $capaian->delete();
        return redirect()->route('capaian.index')->with('success', 'Indikator berhasil dihapus.');
    }

    public function updateBulanan(Request $request, RealisasiBulanan $realisasiBulanan)
    {
        $validated = $request->validate([
            'realisasi' => ['required', 'numeric', 'min:0'],
        ]);

        $capaian = $realisasiBulanan->capaian;
        $validated['persentase'] = $capaian->target > 0
            ? round($validated['realisasi'] / $capaian->target * 100, 2)
            : 0;

        $realisasiBulanan->update($validated);

        $totalRealisasi = $capaian->realisasiBulanan()->sum('realisasi');
        $pctTotal = $capaian->target > 0 ? round($totalRealisasi / $capaian->target * 100, 2) : 0;
        $capaian->update(['realisasi' => $totalRealisasi, 'persentase' => $pctTotal]);

        return redirect()->route('capaian.index')->with('success', 'Realisasi bulanan berhasil diperbarui.');
    }
}
