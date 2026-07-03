@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
{{-- Stats Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

    <div class="card p-5 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Output</p>
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
        </div>
        <p class="text-4xl font-bold text-slate-800 mb-1">{{ $totalOutput }}</p>
        <p class="text-xs text-slate-400 font-medium">Output Tahun {{ $tahun }}</p>
    </div>

    <div class="card p-5 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pagu</p>
            <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800 mb-1">Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</p>
        <p class="text-xs text-slate-400 font-medium">Pagu Anggaran</p>
    </div>

    <div class="card p-5 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Realisasi Anggaran</p>
            <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800 mb-2">Rp {{ number_format($totalRealisasiAnggaran, 0, ',', '.') }}</p>
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <p class="text-xs text-slate-400 font-medium">Capaian</p>
                <p class="text-xs font-bold {{ $pctAnggaran >= 80 ? 'text-emerald-600' : ($pctAnggaran >= 50 ? 'text-amber-600' : 'text-red-500') }}">{{ $pctAnggaran }}%</p>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2">
                <div class="h-2 rounded-full transition-all {{ $pctAnggaran >= 80 ? 'bg-emerald-500' : ($pctAnggaran >= 50 ? 'bg-amber-400' : 'bg-red-400') }}" style="width: {{ min($pctAnggaran, 100) }}%"></div>
            </div>
        </div>
    </div>

    <div class="card p-5 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Realisasi Volume</p>
            <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <p class="text-4xl font-bold {{ $pctVolume >= 80 ? 'text-emerald-600' : ($pctVolume >= 50 ? 'text-amber-600' : 'text-red-500') }} mb-2">{{ $pctVolume }}%</p>
        <div>
            <div class="w-full bg-slate-100 rounded-full h-2">
                <div class="h-2 rounded-full {{ $pctVolume >= 80 ? 'bg-emerald-500' : ($pctVolume >= 50 ? 'bg-amber-400' : 'bg-red-400') }}" style="width: {{ min($pctVolume, 100) }}%"></div>
            </div>
            <p class="text-xs text-slate-400 font-medium mt-1.5">Rata-rata Capaian Output</p>
        </div>
    </div>
</div>

{{-- Notifikasi Perlu Perhatian --}}
@if($perluPerhatian->count())
<div x-data="{ open: true }" x-show="open" class="mb-6">
    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-start gap-3 flex-1 min-w-0">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-red-800">{{ $perluPerhatian->count() }} Output Memerlukan Perhatian</p>
                    <p class="text-xs text-red-600 mt-0.5 mb-3">Output berikut memiliki realisasi volume di bawah 50% dan perlu tindakan segera.</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($perluPerhatian as $item)
                        <a href="{{ route('realisasi.detail', $item->output_id) }}"
                           class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white border border-red-200 rounded-lg text-xs font-semibold text-red-700 hover:bg-red-50 transition-colors">
                            <span class="font-mono">{{ $item->kode_output }}</span>
                            <span class="text-red-400">{{ number_format($item->persentase_volume, 2) }}%</span>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <button @click="open = false" class="p-1 text-red-400 hover:text-red-600 transition-colors flex-shrink-0">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </button>
        </div>
    </div>
</div>
@endif

{{-- Chart --}}
<div class="card p-6 mb-6">
    <div class="flex items-start justify-between mb-5">
        <div>
            <h2 class="text-base font-bold text-slate-800">Grafik Realisasi Bulanan</h2>
            <p class="text-xs text-slate-400 mt-0.5">Rata-rata persentase realisasi output dan anggaran per bulan — Tahun {{ $tahun }}</p>
        </div>
        <div class="flex items-center gap-4 text-xs text-slate-500">
            <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-3 rounded-sm bg-blue-500"></span>Output</span>
            <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-3 rounded-sm bg-emerald-500"></span>Anggaran</span>
        </div>
    </div>
    <canvas id="realisasiChart" height="70"></canvas>
</div>

{{-- Top Realisasi Table --}}
@if($realisasiTerbaru->count())
<div class="card">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-slate-800">Top 5 Realisasi Output</h2>
            <p class="text-xs text-slate-400 mt-0.5">Berdasarkan persentase volume tertinggi</p>
        </div>
        <a href="{{ route('realisasi.index') }}" class="text-xs text-blue-600 font-semibold hover:text-blue-800 transition-colors">Lihat Semua →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Kode Output</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Output</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Vol. Realisasi</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">% Volume</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($realisasiTerbaru as $item)
                <tr class="hover:bg-blue-50/30 transition-colors">
                    <td class="px-6 py-3.5">
                        <span class="font-mono text-xs text-blue-700 bg-blue-50 px-2 py-1 rounded-md font-semibold">{{ $item->kode_output }}</span>
                    </td>
                    <td class="px-6 py-3.5 text-slate-700 max-w-xs">
                        <span class="line-clamp-1 font-medium">{{ $item->output?->nama_output }}</span>
                    </td>
                    <td class="px-6 py-3.5 text-right text-slate-700 font-semibold">{{ number_format($item->volume_akumulatif) }}</td>
                    <td class="px-6 py-3.5 text-right">
                        <span class="font-bold {{ $item->persentase_volume >= 80 ? 'text-emerald-600' : ($item->persentase_volume >= 50 ? 'text-amber-600' : 'text-red-500') }}">
                            {{ number_format($item->persentase_volume, 2) }}%
                        </span>
                    </td>
                    <td class="px-6 py-3.5 text-center">
                        @php
                            $sCls = match($item->status) {
                                'Tercapai'        => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                'Hampir Tercapai' => 'bg-blue-100 text-blue-700 border-blue-200',
                                'Dalam Proses'    => 'bg-amber-100 text-amber-700 border-amber-200',
                                'Perlu Perhatian' => 'bg-red-100 text-red-700 border-red-200',
                                default           => 'bg-slate-100 text-slate-500 border-slate-200',
                            };
                        @endphp
                        <span class="status-badge border {{ $sCls }}">{{ $item->status ?: 'Belum Ada Data' }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div class="card px-6 py-12 text-center">
    <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
        <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
    </div>
    <p class="font-semibold text-slate-500">Belum ada data realisasi</p>
    <p class="text-sm text-slate-400 mt-1">Mulai input realisasi di menu Realisasi.</p>
</div>
@endif
@endsection

@push('scripts')
<script>
const labels = @json(array_values($bulanList));
const dataOutput = @json($chartOutput);
const dataAnggaran = @json($chartAnggaran);

const ctx = document.getElementById('realisasiChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Realisasi Output (%)',
                data: dataOutput,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59,130,246,0.07)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#3b82f6',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
            },
            {
                label: 'Realisasi Anggaran (%)',
                data: dataAnggaran,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16,185,129,0.07)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#10b981',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
            }
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1e293b',
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y.toFixed(2)}%`
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,0.04)' },
                border: { display: false },
                ticks: { callback: v => v + '%', font: { size: 11 }, color: '#94a3b8' }
            },
            x: {
                grid: { display: false },
                border: { display: false },
                ticks: { font: { size: 11 }, color: '#94a3b8' }
            }
        }
    }
});
</script>
@endpush
