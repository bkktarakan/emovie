@extends('layouts.app')
@section('title', 'Laporan')

@section('actions')
    <a href="{{ route('laporan.export.excel') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export Excel
    </a>
    <a href="{{ route('laporan.export.pdf') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
        Export PDF
    </a>
    <a href="{{ route('laporan.cetak') }}" target="_blank"
       class="inline-flex items-center gap-2 px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Cetak
    </a>
@endsection

@section('content')
{{-- Summary Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
    <div class="card p-5">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Total Output</p>
        <p class="text-4xl font-bold text-slate-800 mb-1">{{ $outputs->count() }}</p>
        <p class="text-xs text-slate-400 font-medium">Output Tahun {{ $tahun }}</p>
    </div>
    <div class="card p-5">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Total Pagu</p>
        <p class="text-xl font-bold text-slate-800 mb-1">Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</p>
        <p class="text-xs text-slate-400 font-medium">Pagu Anggaran</p>
    </div>
    <div class="card p-5">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Total Realisasi</p>
        <p class="text-xl font-bold text-emerald-700 mb-1">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</p>
        @php $pctTotal = $totalAnggaran > 0 ? round($totalRealisasi / $totalAnggaran * 100, 2) : 0; @endphp
        <div class="mt-1">
            <div class="w-full bg-slate-100 rounded-full h-1.5 mb-1">
                <div class="h-1.5 rounded-full {{ $pctTotal >= 80 ? 'bg-emerald-500' : ($pctTotal >= 50 ? 'bg-amber-400' : 'bg-red-400') }}" style="width: {{ min($pctTotal, 100) }}%"></div>
            </div>
            <p class="text-xs text-slate-400 font-medium">{{ $pctTotal }}% dari pagu</p>
        </div>
    </div>
</div>

{{-- Tabel Output --}}
<div class="card mb-5">
    <div class="px-6 py-4 border-b border-slate-100">
        <h2 class="font-semibold text-slate-800">Rekapitulasi Output & Realisasi</h2>
        <p class="text-xs text-slate-400 mt-0.5">Tahun Anggaran {{ $tahun }}</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-10">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Kode Output</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Output</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Pagu (Rp)</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Realisasi (Rp)</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">% Anggaran</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">% Volume</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($outputs as $i => $output)
                @php $akm = $output->akumulatif; @endphp
                <tr class="hover:bg-blue-50/30 transition-colors">
                    <td class="px-4 py-3.5 text-slate-400 text-xs font-medium">{{ $i + 1 }}</td>
                    <td class="px-4 py-3.5">
                        <span class="font-mono text-xs text-blue-700 bg-blue-50 px-2 py-1 rounded-md font-semibold">{{ $output->kode_output }}</span>
                    </td>
                    <td class="px-4 py-3.5 text-slate-700 max-w-xs"><span class="line-clamp-2 leading-snug">{{ $output->nama_output }}</span></td>
                    <td class="px-4 py-3.5 text-right text-slate-600 font-medium">{{ number_format($output->anggaran, 0, ',', '.') }}</td>
                    <td class="px-4 py-3.5 text-right text-emerald-700 font-bold">{{ number_format($akm?->anggaran_akumulatif ?? 0, 0, ',', '.') }}</td>
                    <td class="px-4 py-3.5 text-center">
                        @php $pctAng = $akm?->persentase_anggaran ?? 0; @endphp
                        <span class="text-xs font-bold {{ $pctAng >= 80 ? 'text-emerald-600' : ($pctAng >= 50 ? 'text-amber-600' : 'text-red-500') }}">{{ number_format($pctAng, 2) }}%</span>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        @php $pctVol = $akm?->persentase_volume ?? 0; @endphp
                        <span class="text-xs font-bold {{ $pctVol >= 80 ? 'text-emerald-600' : ($pctVol >= 50 ? 'text-amber-600' : 'text-red-500') }}">{{ number_format($pctVol, 2) }}%</span>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        @php
                            $status = $akm?->status ?? '';
                            $sCls = match($status) {
                                'Tercapai'        => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                'Hampir Tercapai' => 'bg-blue-100 text-blue-700 border-blue-200',
                                'Dalam Proses'    => 'bg-amber-100 text-amber-700 border-amber-200',
                                'Perlu Perhatian' => 'bg-red-100 text-red-700 border-red-200',
                                default           => 'bg-slate-100 text-slate-400 border-slate-200',
                            };
                        @endphp
                        <span class="status-badge border {{ $sCls }}">{{ $status ?: 'Belum Mulai' }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Capaian --}}
@if($capaian->count())
<div class="card">
    <div class="px-6 py-4 border-b border-slate-100">
        <h2 class="font-semibold text-slate-800">Capaian Indikator Kinerja</h2>
        <p class="text-xs text-slate-400 mt-0.5">Tahun Anggaran {{ $tahun }}</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-10">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Indikator Kinerja</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Target</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Realisasi</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Persentase</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($capaian as $i => $c)
                <tr class="hover:bg-blue-50/30 transition-colors">
                    <td class="px-4 py-3.5 text-slate-400 text-xs font-medium">{{ $i + 1 }}</td>
                    <td class="px-4 py-3.5 text-slate-700 font-medium">{{ $c->indikator }}</td>
                    <td class="px-4 py-3.5 text-right text-slate-600 font-semibold">{{ $c->target }}</td>
                    <td class="px-4 py-3.5 text-right font-bold {{ $c->realisasi >= $c->target ? 'text-emerald-600' : 'text-slate-700' }}">{{ $c->realisasi }}</td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="text-xs font-bold {{ $c->persentase >= 100 ? 'text-emerald-600' : ($c->persentase >= 50 ? 'text-amber-600' : 'text-red-500') }}">{{ number_format($c->persentase, 2) }}%</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
