@extends('layouts.app')
@section('title', 'Realisasi')

@section('content')
<div class="card" x-data="{ search: '', filterStatus: '' }">
    <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="font-semibold text-slate-800">Realisasi Akumulatif Output</h2>
            <p class="text-xs text-slate-400 mt-0.5">Tahun {{ $tahun }} &mdash; Klik "Detail" untuk input realisasi per bulan</p>
        </div>
        <div class="flex gap-2">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-model="search" placeholder="Cari output..."
                       class="pl-9 pr-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 w-52">
            </div>
            <select x-model="filterStatus" class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                <option value="">Semua Status</option>
                <option value="Tercapai">Tercapai</option>
                <option value="Hampir Tercapai">Hampir Tercapai</option>
                <option value="Dalam Proses">Dalam Proses</option>
                <option value="Perlu Perhatian">Perlu Perhatian</option>
                <option value="">Belum Mulai</option>
            </select>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-10">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Kode Output</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Output</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Vol. Target</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Vol. Realisasi</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">% Volume</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">% Anggaran</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider w-20">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($outputs as $i => $output)
                @php $akm = $output->akumulatif; @endphp
                <tr class="hover:bg-blue-50/30 transition-colors"
                    x-show="(search === '' || '{{ strtolower($output->kode_output) }} {{ strtolower($output->nama_output) }}'.includes(search.toLowerCase())) && (filterStatus === '' || filterStatus === '{{ $akm?->status ?? '' }}')"
                    x-cloak>
                    <td class="px-4 py-3.5 text-slate-400 text-xs font-medium">{{ $i + 1 }}</td>
                    <td class="px-4 py-3.5">
                        <span class="font-mono text-xs text-blue-700 bg-blue-50 px-2 py-1 rounded-md font-semibold">{{ $output->kode_output }}</span>
                    </td>
                    <td class="px-4 py-3.5 text-slate-700 max-w-xs">
                        <span class="line-clamp-2 leading-snug" title="{{ $output->nama_output }}">{{ $output->nama_output }}</span>
                    </td>
                    <td class="px-4 py-3.5 text-right font-semibold text-slate-700">
                        {{ number_format($output->volume) }}
                        <span class="text-xs font-normal text-slate-400 ml-0.5">{{ $output->satuan }}</span>
                    </td>
                    <td class="px-4 py-3.5 text-right font-bold {{ ($akm?->volume_akumulatif ?? 0) > 0 ? 'text-emerald-700' : 'text-slate-300' }}">
                        {{ number_format($akm?->volume_akumulatif ?? 0) }}
                    </td>
                    <td class="px-4 py-3.5">
                        @php $pctVol = $akm?->persentase_volume ?? 0; @endphp
                        <div class="flex items-center gap-2 min-w-[110px]">
                            <div class="flex-1 bg-slate-100 rounded-full h-2">
                                <div class="h-2 rounded-full {{ $pctVol >= 80 ? 'bg-emerald-500' : ($pctVol >= 50 ? 'bg-amber-400' : 'bg-red-400') }}"
                                     style="width: {{ min($pctVol, 100) }}%"></div>
                            </div>
                            <span class="text-xs font-bold {{ $pctVol >= 80 ? 'text-emerald-600' : ($pctVol >= 50 ? 'text-amber-600' : 'text-red-500') }} w-12 text-right">{{ number_format($pctVol, 2) }}%</span>
                        </div>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        @php $pctAng = $akm?->persentase_anggaran ?? 0; @endphp
                        <span class="text-xs font-bold {{ $pctAng >= 80 ? 'text-emerald-600' : ($pctAng >= 50 ? 'text-amber-600' : 'text-red-500') }}">{{ number_format($pctAng, 2) }}%</span>
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
                    <td class="px-4 py-3.5 text-center">
                        <a href="{{ route('realisasi.detail', $output->id) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center gap-3 text-slate-400">
                            <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center">
                                <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <p class="font-semibold text-slate-500">Belum ada output</p>
                            <p class="text-sm">Tambahkan output terlebih dahulu di menu Output.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
