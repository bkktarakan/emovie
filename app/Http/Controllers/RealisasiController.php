<?php

namespace App\Http\Controllers;

use App\Models\Output;
use App\Models\Realisasi;
use App\Models\Akumulatif;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RealisasiController extends Controller
{
    public function index()
    {
        $tahun = session('tahun');
        $outputs = Output::with(['akumulatif' => function($q) use ($tahun) {
            $q->where('tahun', $tahun);
        }])->where('tahun', $tahun)->orderBy('kode_output')->get();
        return view('realisasi.index', compact('outputs', 'tahun'));
    }

    public function detail(Output $output)
    {
        $tahun = session('tahun');
        $realisasi = $output->realisasi()->where('tahun', $tahun)->get();
        $akumulatif = $output->akumulatif;
        return view('realisasi.detail', compact('output', 'realisasi', 'akumulatif', 'tahun'));
    }

    public function update(Request $request, Realisasi $realisasi)
    {
        if ((int) $realisasi->tahun !== (int) session('tahun')) {
            abort(403, 'Tidak dapat mengubah data tahun lain.');
        }

        $validated = $request->validate([
            'realisasi_volume'   => ['required', 'integer', 'min:0'],
            'realisasi_anggaran' => ['required', 'integer', 'min:0'],
            'kemanfaatan'        => ['nullable', 'in:Sudah dimanfaatkan,Belum dimanfaatkan'],
            'keterangan'         => ['nullable', 'string', 'max:1000'],
        ]);

        $output = $realisasi->output;

        $validated['persentase_volume'] = $output->volume > 0
            ? round($validated['realisasi_volume'] / $output->volume * 100, 6)
            : 0;

        $validated['persentase_anggaran'] = $output->anggaran > 0
            ? round($validated['realisasi_anggaran'] / $output->anggaran * 100, 6)
            : 0;

        $tahun = (int) $realisasi->tahun;

        DB::transaction(function () use ($realisasi, $validated, $output, $tahun) {
            $realisasi->update($validated);
            $this->recalcCumulativePcro($output, $tahun);
            $this->updateAkumulatif($output, $tahun);
        });

        ActivityLog::record('Edit', 'Realisasi', "Update realisasi {$output->kode_output} bulan {$realisasi->bulan} — vol: {$validated['realisasi_volume']}");
        return redirect()->route('realisasi.detail', $output->id)->with('success', 'Realisasi bulan ' . $realisasi->bulan . ' berhasil diperbarui.');
    }

    private function recalcCumulativePcro(Output $output, int $tahun): void
    {
        $cumulative = 0;
        foreach ($output->realisasi()->where('tahun', $tahun)->orderBy('urutan_bulan')->get() as $r) {
            $cumulative += $r->persentase_volume;
            $r->update(['pcro' => round($cumulative, 2)]);
        }
    }

    private function updateAkumulatif(Output $output, int $tahun): void
    {
        $realisasiAll = $output->realisasi()->where('tahun', $tahun)->get();

        $volAkumulatif = $realisasiAll->sum('realisasi_volume');
        $angAkumulatif = $realisasiAll->sum('realisasi_anggaran');
        $pctVol = $output->volume > 0   ? round($volAkumulatif / $output->volume   * 100, 6) : 0;
        $pctAng = $output->anggaran > 0 ? round($angAkumulatif / $output->anggaran * 100, 6) : 0;
        $status = $volAkumulatif <= 0 ? '' : match (true) {
            $pctVol >= 100 => 'Tercapai',
            $pctVol >= 75  => 'Hampir Tercapai',
            $pctVol >= 50  => 'Dalam Proses',
            default        => 'Perlu Perhatian',
        };

        Akumulatif::updateOrCreate(
            ['output_id' => $output->id, 'tahun' => $tahun],
            [
                'kode_output'         => $output->kode_output,
                'volume_akumulatif'   => $volAkumulatif,
                'persentase_volume'   => $pctVol,
                'anggaran_akumulatif' => $angAkumulatif,
                'persentase_anggaran' => $pctAng,
                'status'              => $status,
                'pcro'                => round($pctVol, 2),
            ]
        );
    }
}
