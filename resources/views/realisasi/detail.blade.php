@extends('layouts.app')
@section('title', 'Detail Realisasi')

@section('actions')
    <a href="{{ route('realisasi.index') }}" class="btn-secondary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
@endsection

@section('content')
{{-- Output Info Card --}}
<div class="card p-6 mb-5">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="md:col-span-2">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Output</p>
            <span class="inline-block font-mono text-xs text-blue-700 bg-blue-50 px-2.5 py-1 rounded-md font-semibold mb-2">{{ $output->kode_output }}</span>
            <p class="text-base font-semibold text-slate-800 leading-snug">{{ $output->nama_output }}</p>
        </div>
        <div class="grid grid-cols-3 gap-3">
            <div class="bg-slate-50 border border-slate-100 rounded-xl p-3 text-center">
                <p class="text-xs font-medium text-slate-400 mb-1">Target</p>
                <p class="font-bold text-slate-800 text-lg">{{ number_format($output->volume) }}</p>
                <p class="text-xs text-slate-400">{{ $output->satuan }}</p>
            </div>
            <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-3 text-center">
                <p class="text-xs font-medium text-slate-400 mb-1">Vol. Realisasi</p>
                <p class="font-bold text-emerald-700 text-lg">{{ number_format($akumulatif?->volume_akumulatif ?? 0) }}</p>
                <p class="text-xs font-semibold text-emerald-600">{{ number_format($akumulatif?->persentase_volume ?? 0, 2) }}%</p>
            </div>
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 text-center">
                <p class="text-xs font-medium text-slate-400 mb-1">Anggaran</p>
                <p class="font-bold text-blue-700 text-lg">{{ number_format($akumulatif?->persentase_anggaran ?? 0, 2) }}%</p>
                <p class="text-xs text-blue-500">Rp {{ number_format(($akumulatif?->anggaran_akumulatif ?? 0) / 1000000, 1) }}Jt</p>
            </div>
        </div>
    </div>

    @php
        $pctVol = $akumulatif?->persentase_volume ?? 0;
        $status = $akumulatif?->status ?? '';
        $statusCls = match($status) {
            'Tercapai'       => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'Hampir Tercapai'=> 'bg-blue-100 text-blue-700 border-blue-200',
            'Dalam Proses'   => 'bg-amber-100 text-amber-700 border-amber-200',
            'Perlu Perhatian'=> 'bg-red-100 text-red-700 border-red-200',
            default          => 'bg-slate-100 text-slate-500 border-slate-200',
        };
    @endphp
    <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-3">
        <div class="flex-1 bg-slate-100 rounded-full h-2.5">
            <div class="h-2.5 rounded-full transition-all {{ $pctVol >= 80 ? 'bg-emerald-500' : ($pctVol >= 50 ? 'bg-amber-400' : 'bg-red-400') }}"
                 style="width: {{ min($pctVol, 100) }}%"></div>
        </div>
        <span class="status-badge border {{ $statusCls }}">{{ $status ?: 'Belum Mulai' }}</span>
    </div>
</div>

{{-- Grafik Tren Bulanan --}}
<div class="card p-5 mb-5">
    <h3 class="text-sm font-bold text-slate-700 mb-4">Tren Realisasi Bulanan</h3>
    <canvas id="trendChart" height="60"></canvas>
</div>

{{-- Realisasi per Bulan --}}
<div class="card" x-data="{}">
    <div class="px-6 py-4 border-b border-slate-100">
        <h2 class="font-semibold text-slate-800">Realisasi Per Bulan</h2>
        <p class="text-xs text-slate-400 mt-0.5">Klik "Edit" untuk memperbarui data realisasi</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Bulan</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Vol. Realisasi</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">% Volume</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Anggaran (Rp)</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">% Anggaran</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">PCRO</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Kemanfaatan</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider w-16">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($realisasi as $r)
                <tr class="hover:bg-blue-50/30 transition-colors {{ $r->realisasi_volume > 0 ? 'bg-emerald-50/20' : '' }}">
                    <td class="px-4 py-3.5 font-semibold text-slate-700">{{ $r->bulan }}</td>
                    <td class="px-4 py-3.5 text-right {{ $r->realisasi_volume > 0 ? 'text-emerald-700 font-bold' : 'text-slate-300' }}">
                        {{ $r->realisasi_volume > 0 ? number_format($r->realisasi_volume) : '—' }}
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="text-xs font-semibold {{ $r->persentase_volume > 0 ? 'text-emerald-600' : 'text-slate-300' }}">
                            {{ $r->persentase_volume > 0 ? number_format($r->persentase_volume, 2).'%' : '—' }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5 text-right {{ $r->realisasi_anggaran > 0 ? 'text-blue-700 font-medium' : 'text-slate-300' }}">
                        {{ $r->realisasi_anggaran > 0 ? number_format($r->realisasi_anggaran, 0, ',', '.') : '—' }}
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="text-xs font-semibold {{ $r->persentase_anggaran > 0 ? 'text-blue-600' : 'text-slate-300' }}">
                            {{ $r->persentase_anggaran > 0 ? number_format($r->persentase_anggaran, 2).'%' : '—' }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        @if($r->pcro > 0)
                            <span class="status-badge bg-violet-100 text-violet-700 border border-violet-200">{{ number_format($r->pcro, 2) }}</span>
                        @else
                            <span class="text-slate-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-xs text-slate-500 max-w-[180px] truncate">{{ $r->kemanfaatan ?: '—' }}</td>
                    <td class="px-4 py-3.5 text-center">
                        <button @click="$dispatch('open-modal', 'edit-real-{{ $r->id }}')"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-blue-600 hover:text-white text-slate-600 text-xs font-semibold rounded-lg transition-all">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

{{-- ===== MODALS (di luar tabel agar HTML valid) ===== --}}
@foreach($realisasi as $r)
@push('modals')
<div x-data="{ open: false }" @open-modal.window="open = $event.detail === 'edit-real-{{ $r->id }}'"
     x-show="open" x-cloak class="modal-overlay"
     x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="modal-backdrop" @click="open = false"></div>
    <div class="modal-box max-w-md"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         @click.stop>
        <div class="modal-header">
            <div>
                <h3 class="font-semibold text-slate-800">Edit Realisasi</h3>
                <p class="text-xs text-blue-600 font-semibold mt-0.5">{{ $r->bulan }} {{ $tahun }}</p>
            </div>
            <button @click="open = false" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('realisasi.update', $r->id) }}" class="modal-body">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label-form">Volume Realisasi</label>
                    <input type="number" name="realisasi_volume" value="{{ $r->realisasi_volume }}" min="0" required class="input-form">
                </div>
                <div>
                    <label class="label-form">Realisasi Anggaran (Rp)</label>
                    <input type="number" name="realisasi_anggaran" value="{{ $r->realisasi_anggaran }}" min="0" required class="input-form">
                </div>
            </div>
            <div>
                <label class="label-form">Kemanfaatan</label>
                <select name="kemanfaatan" class="input-form">
                    <option value="">-- Pilih --</option>
                    <option value="Sudah dimanfaatkan" {{ $r->kemanfaatan === 'Sudah dimanfaatkan' ? 'selected' : '' }}>Sudah dimanfaatkan</option>
                    <option value="Belum dimanfaatkan" {{ $r->kemanfaatan === 'Belum dimanfaatkan' ? 'selected' : '' }}>Belum dimanfaatkan</option>
                </select>
            </div>
            <div>
                <label class="label-form">Keterangan</label>
                <textarea name="keterangan" rows="2" maxlength="1000" class="input-form">{{ $r->keterangan }}</textarea>
            </div>
            <div class="modal-footer">
                <button type="button" @click="open = false" class="btn-secondary flex-1">Batal</button>
                <button type="submit" class="btn-primary flex-1 justify-center">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endpush
@endforeach

@push('scripts')
<script>
(function() {
    const labels  = @json($realisasi->pluck('bulan')->values());
    const volData = @json($realisasi->pluck('persentase_volume')->values());
    const angData = @json($realisasi->pluck('persentase_anggaran')->values());
    const pcroData = @json($realisasi->pluck('pcro')->values());

    new Chart(document.getElementById('trendChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: '% Volume',
                    data: volData,
                    backgroundColor: 'rgba(59,130,246,0.7)',
                    borderColor: '#3b82f6',
                    borderWidth: 1,
                    borderRadius: 4,
                    order: 2,
                },
                {
                    label: '% Anggaran',
                    data: angData,
                    backgroundColor: 'rgba(16,185,129,0.7)',
                    borderColor: '#10b981',
                    borderWidth: 1,
                    borderRadius: 4,
                    order: 2,
                },
                {
                    label: 'PCRO (kumulatif)',
                    data: pcroData,
                    type: 'line',
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245,158,11,0.1)',
                    fill: false,
                    tension: 0.4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#f59e0b',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    order: 1,
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    position: 'top',
                    labels: { font: { size: 11 }, boxWidth: 12, padding: 16 }
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: { label: c => ` ${c.dataset.label}: ${c.parsed.y.toFixed(2)}%` }
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
                    ticks: { font: { size: 10 }, color: '#94a3b8' }
                }
            }
        }
    });
})();
</script>
@endpush
