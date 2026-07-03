@extends('layouts.app')
@section('title', $label)

@section('actions')
    <button @click="$dispatch('open-modal', 'tambah-komponen')" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Komponen
    </button>
@endsection

@section('content')

{{-- Sub-menu tabs --}}
<div class="flex gap-1 mb-5 bg-slate-100 p-1 rounded-xl w-fit">
    @foreach(['perencanaan' => 'Perencanaan', 'pengukuran' => 'Pengukuran', 'pelaporan' => 'Pelaporan', 'evaluasi' => 'Evaluasi'] as $t => $l)
    <a href="{{ route('komponen.index', $t) }}"
       class="px-4 py-2 rounded-lg text-sm font-semibold transition-all {{ $type === $t ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-white/60' }}">{{ $l }}</a>
    @endforeach
</div>

<div class="space-y-4" x-data="{}">
    @forelse($komponen as $komp)
    <div class="card overflow-hidden">
        <div class="px-6 py-4 flex items-center justify-between border-b border-slate-100">
            <div class="flex items-center gap-3 flex-1">
                <div class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0"></div>
                <p class="font-semibold text-slate-800">{{ $komp->komponen }}</p>
            </div>
            <div class="flex items-center gap-1 ml-4">
                <button @click="$dispatch('open-modal', 'edit-komp-{{ $komp->id }}')"
                        class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                <form method="POST" action="{{ route('komponen.destroy', $komp->id) }}"
                      onsubmit="return confirm('Hapus komponen ini beserta semua dokumen pendukungnya?')" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
                <button @click="$dispatch('open-modal', 'tambah-sub-{{ $komp->id }}')"
                        class="ml-1 inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition-colors">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Dokumen
                </button>
            </div>
        </div>

        @if($komp->subkomponen->count())
        <div class="divide-y divide-slate-100">
            @foreach($komp->subkomponen as $sub)
            <div class="px-6 py-3 flex items-center gap-3 hover:bg-slate-50/70 transition-colors group">
                <div class="w-1.5 h-1.5 rounded-full bg-slate-300 flex-shrink-0 group-hover:bg-blue-400 transition-colors"></div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-slate-700 font-medium">{{ $sub->dakung }}</p>
                    @if($sub->link)
                    <a href="{{ $sub->link }}" target="_blank" rel="noopener noreferrer"
                       class="text-xs text-blue-600 hover:text-blue-800 hover:underline truncate block mt-0.5 font-mono">{{ $sub->link }}</a>
                    @endif
                </div>
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button @click="$dispatch('open-modal', 'edit-sub-{{ $sub->id }}')"
                            class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <form method="POST" action="{{ route('subkomponen.destroy', $sub->id) }}"
                          onsubmit="return confirm('Hapus dokumen ini?')" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="px-6 py-4 text-xs text-slate-400 italic">Belum ada dokumen pendukung. Klik "+ Dokumen" untuk menambahkan.</div>
        @endif
    </div>
    @empty
    <div class="card px-6 py-16 text-center">
        <div class="flex flex-col items-center gap-3 text-slate-400">
            <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center">
                <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <p class="font-semibold text-slate-500">Belum ada komponen {{ $label }}</p>
            <p class="text-sm">Klik "Tambah Komponen" untuk memulai.</p>
        </div>
    </div>
    @endforelse
</div>
@endsection

{{-- ===== MODALS ===== --}}

{{-- Tambah Komponen Modal --}}
@push('modals')
<div x-data="{ open: false }" @open-modal.window="open = $event.detail === 'tambah-komponen'"
     x-show="open" x-cloak class="modal-overlay"
     x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="modal-backdrop" @click="open = false"></div>
    <div class="modal-box max-w-md"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         @click.stop>
        <div class="modal-header">
            <h3 class="font-semibold text-slate-800">Tambah Komponen {{ $label }}</h3>
            <button @click="open = false" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('komponen.store', $type) }}" class="modal-body">
            @csrf
            <div>
                <label class="label-form">Nama Komponen <span class="text-red-500">*</span></label>
                <textarea name="komponen" rows="3" required class="input-form" placeholder="Deskripsi komponen...">{{ old('komponen') }}</textarea>
            </div>
            <div class="modal-footer">
                <button type="button" @click="open = false" class="btn-secondary flex-1">Batal</button>
                <button type="submit" class="btn-primary flex-1 justify-center">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endpush

{{-- Per-komponen modals --}}
@foreach($komponen as $komp)

{{-- Tambah Sub Modal --}}
@push('modals')
<div x-data="{ open: false }" @open-modal.window="open = $event.detail === 'tambah-sub-{{ $komp->id }}'"
     x-show="open" x-cloak class="modal-overlay"
     x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="modal-backdrop" @click="open = false"></div>
    <div class="modal-box max-w-md"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         @click.stop>
        <div class="modal-header">
            <h3 class="font-semibold text-slate-800">Tambah Dokumen Pendukung</h3>
            <button @click="open = false" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('subkomponen.store', $type) }}" class="modal-body">
            @csrf
            <input type="hidden" name="komponen_id" value="{{ $komp->id }}">
            <p class="text-xs text-slate-500 -mt-1 pb-1">Komponen: <span class="font-medium text-slate-700">{{ Str::limit($komp->komponen, 60) }}</span></p>
            <div>
                <label class="label-form">Nama Dokumen / Bukti</label>
                <input type="text" name="dakung" required class="input-form" placeholder="Nama dokumen pendukung...">
            </div>
            <div>
                <label class="label-form">Link <span class="text-slate-400 font-normal">(opsional)</span></label>
                <input type="url" name="link" class="input-form" placeholder="https://...">
            </div>
            <div class="modal-footer">
                <button type="button" @click="open = false" class="btn-secondary flex-1">Batal</button>
                <button type="submit" class="btn-primary flex-1 justify-center">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endpush

{{-- Edit Komponen Modal --}}
@push('modals')
<div x-data="{ open: false }" @open-modal.window="open = $event.detail === 'edit-komp-{{ $komp->id }}'"
     x-show="open" x-cloak class="modal-overlay"
     x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="modal-backdrop" @click="open = false"></div>
    <div class="modal-box max-w-md"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         @click.stop>
        <div class="modal-header">
            <h3 class="font-semibold text-slate-800">Edit Komponen</h3>
            <button @click="open = false" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('komponen.update', $komp->id) }}" class="modal-body">
            @csrf @method('PUT')
            <div>
                <label class="label-form">Nama Komponen</label>
                <textarea name="komponen" rows="3" required class="input-form">{{ $komp->komponen }}</textarea>
            </div>
            <div class="modal-footer">
                <button type="button" @click="open = false" class="btn-secondary flex-1">Batal</button>
                <button type="submit" class="btn-primary flex-1 justify-center">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endpush

{{-- Edit Sub Modals --}}
@foreach($komp->subkomponen as $sub)
@push('modals')
<div x-data="{ open: false }" @open-modal.window="open = $event.detail === 'edit-sub-{{ $sub->id }}'"
     x-show="open" x-cloak class="modal-overlay"
     x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="modal-backdrop" @click="open = false"></div>
    <div class="modal-box max-w-md"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         @click.stop>
        <div class="modal-header">
            <h3 class="font-semibold text-slate-800">Edit Dokumen Pendukung</h3>
            <button @click="open = false" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('subkomponen.update', $sub->id) }}" class="modal-body">
            @csrf @method('PUT')
            <div>
                <label class="label-form">Nama Dokumen</label>
                <input type="text" name="dakung" value="{{ $sub->dakung }}" required class="input-form">
            </div>
            <div>
                <label class="label-form">Link <span class="text-slate-400 font-normal">(opsional)</span></label>
                <input type="url" name="link" value="{{ $sub->link }}" class="input-form" placeholder="https://...">
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
