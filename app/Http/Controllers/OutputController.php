<?php

namespace App\Http\Controllers;

use App\Models\Output;
use App\Models\Realisasi;
use App\Models\Akumulatif;
use App\Models\ActivityLog;
use App\Imports\OutputImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class OutputController extends Controller
{
    public function index()
    {
        $tahun = session('tahun');
        $outputs = Output::with(['akumulatif' => function($q) use ($tahun) {
            $q->where('tahun', $tahun);
        }])->where('tahun', $tahun)->orderBy('kode_output')->get();
        return view('output.index', compact('outputs', 'tahun'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_output'  => ['required', 'string', 'max:30', 'regex:/^[A-Za-z0-9.\-]+$/'],
            'nama_output'  => ['required', 'string', 'max:500'],
            'volume'       => ['required', 'integer', 'min:1', 'max:999999'],
            'satuan'       => ['required', 'in:' . implode(',', Output::getSatuanOptions())],
            'anggaran'     => ['required', 'integer', 'min:0'],
        ]);

        $tahun = session('tahun');

        if (Output::where('kode_output', $validated['kode_output'])->where('tahun', $tahun)->exists()) {
            return back()->with('error', 'Kode Output sudah ada untuk tahun ini.')->withInput();
        }

        DB::transaction(function () use ($validated, $tahun) {
            $output = Output::create([
                'tahun'       => $tahun,
                'kode_output' => strtoupper($validated['kode_output']),
                'nama_output' => $validated['nama_output'],
                'volume'      => $validated['volume'],
                'satuan'      => $validated['satuan'],
                'anggaran'    => $validated['anggaran'],
            ]);

            Akumulatif::create([
                'output_id'          => $output->id,
                'tahun'              => $tahun,
                'kode_output'        => $output->kode_output,
                'volume_akumulatif'  => 0,
                'persentase_volume'  => 0,
                'anggaran_akumulatif'=> 0,
                'persentase_anggaran'=> 0,
                'status'             => '',
                'pcro'               => 0,
            ]);

            foreach (Output::getBulanList() as $urutan => $bulan) {
                Realisasi::create([
                    'output_id'           => $output->id,
                    'tahun'               => $tahun,
                    'kode_output'         => $output->kode_output,
                    'bulan'               => $bulan,
                    'urutan_bulan'        => $urutan,
                    'realisasi_volume'    => 0,
                    'persentase_volume'   => 0,
                    'realisasi_anggaran'  => 0,
                    'persentase_anggaran' => 0,
                    'pcro'                => 0,
                    'kemanfaatan'         => '',
                    'keterangan'          => '',
                ]);
            }
        });

        ActivityLog::record('Tambah', 'Output', "Menambahkan output {$validated['kode_output']} — {$validated['nama_output']}");
        return redirect()->route('output.index')->with('success', 'Output berhasil ditambahkan.');
    }

    public function update(Request $request, Output $output)
    {
        $validated = $request->validate([
            'nama_output' => ['required', 'string', 'max:500'],
            'volume'      => ['required', 'integer', 'min:1', 'max:999999'],
            'satuan'      => ['required', 'in:' . implode(',', Output::getSatuanOptions())],
            'anggaran'    => ['required', 'integer', 'min:0'],
        ]);

        $volumeChanged   = $output->volume   !== (int) $validated['volume'];
        $anggaranChanged = $output->anggaran !== (int) $validated['anggaran'];

        DB::transaction(function () use ($output, $validated, $volumeChanged, $anggaranChanged) {
            $output->update($validated);
            if ($volumeChanged || $anggaranChanged) {
                $this->rekalkulasiOutput($output);
            }
        });

        ActivityLog::record('Edit', 'Output', "Memperbarui output {$output->kode_output}");
        return redirect()->route('output.index')->with('success', 'Output berhasil diperbarui.');
    }

    private function rekalkulasiOutput(Output $output): void
    {
        $output->refresh();

        foreach ($output->realisasi as $r) {
            $r->update([
                'persentase_volume'   => $output->volume > 0
                    ? round($r->realisasi_volume / $output->volume * 100, 6) : 0,
                'persentase_anggaran' => $output->anggaran > 0
                    ? round($r->realisasi_anggaran / $output->anggaran * 100, 6) : 0,
            ]);
        }

        $akumulatif = $output->akumulatif;
        if (!$akumulatif) return;

        $pctVol = $output->volume > 0
            ? round($akumulatif->volume_akumulatif / $output->volume * 100, 6) : 0;
        $pctAng = $output->anggaran > 0
            ? round($akumulatif->anggaran_akumulatif / $output->anggaran * 100, 6) : 0;

        $status = $akumulatif->volume_akumulatif <= 0 ? '' : match (true) {
            $pctVol >= 100 => 'Tercapai',
            $pctVol >= 75  => 'Hampir Tercapai',
            $pctVol >= 50  => 'Dalam Proses',
            default        => 'Perlu Perhatian',
        };

        $akumulatif->update([
            'persentase_volume'   => $pctVol,
            'persentase_anggaran' => $pctAng,
            'status'              => $status,
        ]);
    }

    public function destroy(Output $output)
    {
        $kode = $output->kode_output;
        $nama = $output->nama_output;
        $output->delete();
        ActivityLog::record('Hapus', 'Output', "Menghapus output {$kode} — {$nama}");
        return redirect()->route('output.index')->with('success', 'Output berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:2048']]);
        $import = new OutputImport((int) session('tahun'));
        Excel::import($import, $request->file('file'));
        $msg = "Berhasil import {$import->imported} output.";
        if ($import->skipped > 0) {
            $msg .= " {$import->skipped} baris dilewati (duplikat/tidak valid).";
        }
        return redirect()->route('output.index')->with('success', $msg);
    }

    public function templateImport()
    {
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="template-import-output.csv"'];
        $callback = function () {
            $f = fopen('php://output', 'w');
            fputcsv($f, ['kode_output', 'nama_output', 'volume', 'satuan', 'anggaran']);
            fputcsv($f, ['4249.PEA.001.001', 'Contoh Nama Output', '12', 'Laporan', '100000000']);
            fclose($f);
        };
        return response()->stream($callback, 200, $headers);
    }
}
