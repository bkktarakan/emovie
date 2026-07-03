@extends('layouts.app')
@section('title', 'Riwayat Aktivitas')

@section('content')
<div class="card">
    <div class="px-6 py-4 border-b border-slate-100">
        <h2 class="font-semibold text-slate-800">Riwayat Aktivitas</h2>
        <p class="text-xs text-slate-400 mt-0.5">Log semua perubahan data oleh pengguna</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Waktu</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Pengguna</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Modul</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($logs as $log)
                <tr class="hover:bg-slate-50/60 transition-colors">
                    <td class="px-4 py-3 text-xs text-slate-500 whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($log->created_at)->isoFormat('D MMM Y, HH:mm') }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($log->user_name ?? 'S', 0, 1)) }}
                            </div>
                            <span class="text-xs font-medium text-slate-700">{{ $log->user_name ?? 'Sistem' }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @php
                            $aksiCls = match($log->aksi) {
                                'Tambah' => 'bg-emerald-100 text-emerald-700',
                                'Edit'   => 'bg-blue-100 text-blue-700',
                                'Hapus'  => 'bg-red-100 text-red-700',
                                'Import' => 'bg-violet-100 text-violet-700',
                                default  => 'bg-slate-100 text-slate-600',
                            };
                        @endphp
                        <span class="status-badge {{ $aksiCls }}">{{ $log->aksi }}</span>
                    </td>
                    <td class="px-4 py-3 text-xs font-medium text-slate-600">{{ $log->modul }}</td>
                    <td class="px-4 py-3 text-xs text-slate-500 max-w-sm truncate" title="{{ $log->deskripsi }}">{{ $log->deskripsi }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center">
                        <p class="text-slate-400 font-medium">Belum ada aktivitas tercatat</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
