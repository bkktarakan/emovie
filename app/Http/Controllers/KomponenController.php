<?php

namespace App\Http\Controllers;

use App\Models\Komponen;
use App\Models\Subkomponen;
use Illuminate\Http\Request;

class KomponenController extends Controller
{
    private static array $menuMap = [
        'perencanaan' => 1,
        'pengukuran'  => 2,
        'pelaporan'   => 3,
        'evaluasi'    => 4,
    ];

    private function resolveMenu(string $type): int
    {
        abort_if(!isset(self::$menuMap[$type]), 404);
        return self::$menuMap[$type];
    }

    public function index(string $type)
    {
        $subMenu = $this->resolveMenu($type);
        $tahun = session('tahun');
        $label = Komponen::getSubMenuLabel($subMenu);
        $komponen = Komponen::with('subkomponen')
            ->where('sub_menu', $subMenu)
            ->where('tahun', $tahun)
            ->get();
        return view('komponen.index', compact('komponen', 'type', 'label', 'tahun', 'subMenu'));
    }

    public function storeKomponen(Request $request, string $type)
    {
        $subMenu = $this->resolveMenu($type);
        $validated = $request->validate([
            'komponen' => ['required', 'string', 'max:500'],
        ]);

        Komponen::create([
            'sub_menu' => $subMenu,
            'komponen' => $validated['komponen'],
            'tahun'    => session('tahun'),
        ]);

        return redirect()->route('komponen.index', $type)->with('success', 'Komponen berhasil ditambahkan.');
    }

    public function updateKomponen(Request $request, Komponen $komponen)
    {
        $validated = $request->validate([
            'komponen' => ['required', 'string', 'max:500'],
        ]);
        $komponen->update($validated);
        $type = array_search($komponen->sub_menu, self::$menuMap);
        return redirect()->route('komponen.index', $type)->with('success', 'Komponen berhasil diperbarui.');
    }

    public function destroyKomponen(Komponen $komponen)
    {
        $type = array_search($komponen->sub_menu, self::$menuMap);
        $komponen->delete();
        return redirect()->route('komponen.index', $type)->with('success', 'Komponen berhasil dihapus.');
    }

    public function storeSubkomponen(Request $request, string $type)
    {
        $this->resolveMenu($type);
        $validated = $request->validate([
            'komponen_id' => ['required', 'exists:komponen,id'],
            'dakung'      => ['required', 'string', 'max:500'],
            'link'        => ['nullable', 'url', 'max:500'],
        ]);

        Subkomponen::create([
            'komponen_id' => $validated['komponen_id'],
            'dakung'      => $validated['dakung'],
            'link'        => $validated['link'] ?? null,
            'tahun'       => session('tahun'),
        ]);

        return redirect()->route('komponen.index', $type)->with('success', 'Dokumen pendukung berhasil ditambahkan.');
    }

    public function updateSubkomponen(Request $request, Subkomponen $subkomponen)
    {
        $validated = $request->validate([
            'dakung' => ['required', 'string', 'max:500'],
            'link'   => ['nullable', 'url', 'max:500'],
        ]);
        $subkomponen->update($validated);
        $type = array_search($subkomponen->komponen->sub_menu, self::$menuMap);
        return redirect()->route('komponen.index', $type)->with('success', 'Dokumen pendukung berhasil diperbarui.');
    }

    public function destroySubkomponen(Subkomponen $subkomponen)
    {
        $type = array_search($subkomponen->komponen->sub_menu, self::$menuMap);
        $subkomponen->delete();
        return redirect()->route('komponen.index', $type)->with('success', 'Dokumen pendukung berhasil dihapus.');
    }
}
