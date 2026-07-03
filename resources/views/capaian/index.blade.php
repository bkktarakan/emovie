@extends('layouts.app')
@section('title', 'Indikator Kinerja')

@section('actions')
    <button @click="$dispatch('open-modal', 'tambah-capaian')" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Indikator
    </button>
@endsection

@section('content')
<div class="space-y-4" x-data="{}">
    @forelse($capaian as $c)
    <div class="card overflow-hidden">
        <div class="px-6 py-4 flex items-start justify-between gap-4 border-b border-slate-100">
            <div class="flex-1">
                <p class="font-semibold text-slate-800 leading-snug">{{ $c->indikator }}</p>
                <div class="flex flex-wrap items-center gap-3 mt-2.5">
                    <span class="text-xs text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg font-medium">
                        Target: <strong class="text-slate-700">{{ $c->target }}</strong>
                    </span>
                    <span class="text-xs {{ $c->persentase >= 100 ? 'text-emerald-700 bg-emerald-100' : 'text-slate-500 bg-slate-100' }} px-2.5 py-1 rounded-lg font-medium">
                        Realisasi: <strong>{{ $c->realisasi }}</strong>
                    </span>
                    <span class="status-badge border {{ $c->persentase >= 100 ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : ($c->persentase >= 50 ? 'bg-amber-100 text-amber-700 border-amber-200' : 'bg-red-100 text-red-700 border-red-200') }}">
                        {{ number_format($c->persentase, 2) }}%
                    </span>
                    @if($c->link)
                    <a href="{{ $c->link }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-1.5 text-xs text-blue-600 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded-lg font-medium transition-colors border border-blue-200">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        Data Dukung
                    </a>
                    @endif
                </div>
                <div class="mt-3 w-full bg-slate-100 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all {{ $c->persentase >= 100 ? 'bg-emerald-500' : ($c->persentase >= 50 ? 'bg-amber-400' : 'bg-red-400') }}"
                         style="width: {{ min($c->persentase, 100) }}%"></div>
                </div>
            </div>
            <div class="flex items-center gap-1 flex-shrink-0">
                <button @click="$dispatch('open-modal', 'edit-cap-{{ $c->id }}')"
                        class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                <form method="POST" action="{{ route('capaian.destroy', $c->id) }}"
                      onsubmit="return confirm('Hapus indikator ini? Semua data realisasi bulanan akan ikut terhapus.')" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- Realisasi Bulanan Grid --}}
        <div class="px-6 py-4 bg-slate-50/50">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Realisasi per Bulan — klik bulan untuk update</p>
            <div class="grid grid-cols-6 lg:grid-cols-12 gap-2">
                @foreach($c->realisasiBulanan as $rb)
                <button @click="$dispatch('open-modal', 'edit-rb-{{ $rb->id }}')"
                        class="flex flex-col items-center p-2.5 rounded-xl border transition-all cursor-pointer
                               {{ $rb->realisasi > 0
                                  ? 'border-emerald-200 bg-emerald-50 hover:bg-emerald-100 hover:border-emerald-300'
                                  : 'border-slate-200 bg-white hover:bg-blue-50 hover:border-blue-300' }}">
                    <span class="text-xs font-semibold text-slate-400 mb-0.5">{{ substr($rb->bulan, 0, 3) }}</span>
                    <span class="text-sm font-bold {{ $rb->realisasi > 0 ? 'text-emerald-700' : 'text-slate-300' }}">
                        {{ $rb->realisasi > 0 ? $rb->realisasi : '—' }}
                    </span>
                </button>
                @endforeach
            </div>
        </div>
    </div>
    @empty
    <div class="card px-6 py-16 text-center">
        <div class="flex flex-col items-center gap-3 text-slate-400">
            <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center">
                <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            </div>
            <p class="font-semibold text-slate-500">Belum ada indikator kinerja</p>
            <p class="text-sm">Klik "Tambah Indikator" untuk menambahkan.</p>
        </div>
    </div>
    @endforelse
</div>
@endsection

{{-- ===== MODALS ===== --}}

{{-- Tambah Capaian Modal --}}
@push('modals')
<div x-data="{ open: false }" @open-modal.window="open = $event.detail === 'tambah-capaian'"
     x-show="open" x-cloak class="modal-overlay"
     x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="modal-backdrop" @click="open = false"></div>
    <div class="modal-box max-w-md"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         @click.stop>
        <div class="modal-header">
            <h3 class="font-semibold text-slate-800">Tambah Indikator Kinerja</h3>
            <button @click="open = false" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('capaian.store') }}" class="modal-body">
            @csrf
            <div>
                <label class="label-form">Nama Indikator <span class="text-red-500">*</span></label>
                <textarea name="indikator" rows="3" required class="input-form" placeholder="Deskripsi indikator kinerja...">{{ old('indikator') }}</textarea>
            </div>
            <div>
                <label class="label-form">Target <span class="text-red-500">*</span></label>
                <input type="number" name="target" value="{{ old('target') }}" min="0" step="0.01" required class="input-form" placeholder="0">
            </div>
            <div>
                <label class="label-form">Link Data Dukung <span class="text-slate-400 font-normal">(opsional)</span></label>
                <input type="url" name="link" value="{{ old('link') }}" class="input-form" placeholder="https://...">
            </div>
            <div class="modal-footer">
                <button type="button" @click="open = false" class="btn-secondary flex-1">Batal</button>
                <button type="submit" class="btn-primary flex-1 justify-center">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endpush

{{-- Edit Capaian + Edit Realisasi Bulanan Modals --}}
@foreach($capaian as $c)

@push('modals')
<div x-data="{ open: false }" @open-modal.window="open = $event.detail === 'edit-cap-{{ $c->id }}'"
     x-show="open" x-cloak class="modal-overlay"
     x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="modal-backdrop" @click="open = false"></div>
    <div class="modal-box max-w-md"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         @click.stop>
        <div class="modal-header">
            <h3 class="font-semibold text-slate-800">Edit Indikator</h3>
            <button @click="open = false" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('capaian.update', $c->id) }}" class="modal-body">
            @csrf @method('PUT')
            <div>
                <label class="label-form">Indikator Kinerja</label>
                <textarea name="indikator" rows="3" required class="input-form">{{ $c->indikator }}</textarea>
            </div>
            <div>
                <label class="label-form">Target</label>
                <input type="number" name="target" value="{{ $c->target }}" min="0" step="0.01" required class="input-form">
            </div>
            <div>
                <label class="label-form">Link Data Dukung <span class="text-slate-400 font-normal">(opsional)</span></label>
                <input type="url" name="link" value="{{ $c->link }}" class="input-form" placeholder="https://...">
            </div>
            <div class="modal-footer">
                <button type="button" @click="open = false" class="btn-secondary flex-1">Batal</button>
                <button type="submit" class="btn-primary flex-1 justify-center">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endpush

@foreach($c->realisasiBulanan as $rb)
@push('modals')
<div x-data="{ open: false }" @open-modal.window="open = $event.detail === 'edit-rb-{{ $rb->id }}'"
     x-show="open" x-cloak class="modal-overlay"
     x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="modal-backdrop" @click="open = false"></div>
    <div class="modal-box max-w-sm"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         @click.stop>
        <div class="modal-header">
            <div>
                <h3 class="font-semibold text-slate-800">Realisasi {{ $rb->bulan }}</h3>
                <p class="text-xs text-slate-400 mt-0.5">Target: <strong class="text-slate-600">{{ $c->target }}</strong></p>
            </div>
            <button @click="open = false" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('capaian.bulanan.update', $rb->id) }}" class="modal-body">
            @csrf @method('PUT')
            <p class="text-xs text-slate-500 -mt-1">{{ Str::limit($c->indikator, 80) }}</p>
            <div>
                <label class="label-form">Nilai Realisasi</label>
                <input type="number" name="realisasi" value="{{ $rb->realisasi }}" min="0" step="0.01" required class="input-form">
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

@endforeach
