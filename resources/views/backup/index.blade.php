@extends('layouts.app')
@section('title', 'Backup Database')

@section('actions')
    <form method="POST" action="{{ route('backup.create') }}"
          onsubmit="return confirm('Buat backup database sekarang?')">
        @csrf
        <button type="submit" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
            Buat Backup Sekarang
        </button>
    </form>
@endsection

@section('content')
<div class="card">
    <div class="px-6 py-4 border-b border-slate-100">
        <h2 class="font-semibold text-slate-800">File Backup Database</h2>
        <p class="text-xs text-slate-400 mt-0.5">File disimpan di <code class="bg-slate-100 px-1 rounded">storage/backups/</code></p>
    </div>

    @if(count($files))
    <div class="divide-y divide-slate-100">
        @foreach($files as $f)
        <div class="flex items-center justify-between px-6 py-4 hover:bg-slate-50 transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582 4 8 4m0 0c4.418 0 8-1.79 8-4"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800 font-mono">{{ $f['name'] }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $f['size'] }} KB &mdash; {{ \Carbon\Carbon::createFromTimestamp($f['time'])->isoFormat('D MMM Y, HH:mm') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('backup.download', $f['name']) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download
                </a>
                <form method="POST" action="{{ route('backup.destroy', $f['name']) }}"
                      onsubmit="return confirm('Hapus file backup ini?')" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Hapus
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="px-6 py-16 text-center">
        <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
            <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582 4 8 4m0 0c4.418 0 8-1.79 8-4"/></svg>
        </div>
        <p class="font-semibold text-slate-500">Belum ada file backup</p>
        <p class="text-sm text-slate-400 mt-1">Klik "Buat Backup Sekarang" untuk membuat backup pertama.</p>
    </div>
    @endif
</div>
@endsection
