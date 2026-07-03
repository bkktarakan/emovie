<?php

namespace App\Http\Controllers;

use App\Models\RencanaKegiatan;
use App\Models\RencanaKegiatanLink;
use Illuminate\Http\Request;

class RencanaKegiatanController extends Controller
{
    public function index()
    {
        $tahun   = (int) session('tahun', date('Y'));
        $wilayah = RencanaKegiatan::orderBy('urutan')
            ->with(['links' => fn($q) => $q->where('tahun', $tahun)])
            ->get();

        return view('rencana-kegiatan.index', compact('wilayah', 'tahun'));
    }

    public function update(Request $request, RencanaKegiatan $rencanaKegiatan)
    {
        $tahun     = (int) session('tahun', date('Y'));
        $validated = $request->validate([
            'link' => ['nullable', 'url', 'max:500'],
        ]);

        RencanaKegiatanLink::updateOrCreate(
            ['wilayah_id' => $rencanaKegiatan->id, 'tahun' => $tahun],
            ['link' => $validated['link'] ?? null]
        );

        return redirect()->route('rencana-kegiatan.index')
            ->with('success', 'Link ' . $rencanaKegiatan->nama_wilayah . ' tahun ' . $tahun . ' berhasil diperbarui.');
    }
}
