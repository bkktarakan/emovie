@extends('layouts.app')

@section('title', 'Rencana Kegiatan')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">

    {{-- Header card --}}
    <div class="card p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
        </div>
        <div>
            <h2 class="text-lg font-bold text-slate-800">Rencana Kegiatan</h2>
            <p class="text-sm text-slate-500">Link data dukung per wilayah kerja — Tahun Anggaran <span class="font-semibold text-teal-600">{{ $tahun }}</span>.</p>
        </div>
    </div>

    {{-- List Wilayah --}}
    <div class="card divide-y divide-slate-100">
        @foreach($wilayah as $item)
        @php $linkRow = $item->links->first(); $linkUrl = $linkRow?->link; @endphp
        <div class="flex items-center gap-4 px-5 py-4 hover:bg-slate-50 transition-colors">

            {{-- Nomor urut --}}
            <div class="w-8 h-8 rounded-full bg-teal-50 border border-teal-200 flex items-center justify-center flex-shrink-0">
                <span class="text-xs font-bold text-teal-700">{{ $item->urutan }}</span>
            </div>

            {{-- Nama wilayah + link --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-800">{{ $item->nama_wilayah }}</p>
                @if($linkUrl)
                    <a href="{{ $linkUrl }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-1 text-xs text-teal-600 hover:text-teal-700 mt-0.5 truncate max-w-full">
                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                        <span class="truncate">{{ $linkUrl }}</span>
                    </a>
                @else
                    <p class="text-xs text-slate-400 mt-0.5">Belum ada link untuk tahun {{ $tahun }}</p>
                @endif
            </div>

            {{-- Status badge --}}
            @if($linkUrl)
                <span class="status-badge bg-teal-50 text-teal-700 border border-teal-200 flex-shrink-0">Ada Link</span>
            @else
                <span class="status-badge bg-slate-100 text-slate-500 flex-shrink-0">Belum</span>
            @endif

            {{-- Tombol Edit --}}
            <button @click="$dispatch('open-modal-rk', {
                        id:   {{ $item->id }},
                        nama: '{{ addslashes($item->nama_wilayah) }}',
                        link: '{{ $linkUrl ?? '' }}'
                    })"
                    class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </button>
        </div>
        @endforeach
    </div>

</div>
@endsection

{{-- Modal Edit Link --}}
@push('modals')
<div x-data="{
        open: false,
        id:   null,
        nama: '',
        link: '',
    }"
    @open-modal-rk.window="
        id   = $event.detail.id;
        nama = $event.detail.nama;
        link = $event.detail.link;
        open = true;
    "
    x-show="open"
    x-cloak>

    <div class="modal-overlay">
        <div class="modal-backdrop" @click="open = false"></div>
        <div class="modal-box max-w-lg"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

            <div class="modal-header">
                <div>
                    <h3 class="text-base font-semibold text-slate-800">Edit Link Data Dukung</h3>
                    <p class="text-sm text-slate-500 mt-0.5">
                        <span x-text="nama"></span>
                        &mdash; Tahun <span class="font-semibold text-teal-600">{{ $tahun }}</span>
                    </p>
                </div>
                <button @click="open = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>

            <form :action="'{{ url('rencana-kegiatan') }}/' + id" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div>
                        <label class="label-form">
                            Link Data Dukung
                            <span class="text-slate-400 font-normal">(opsional)</span>
                        </label>
                        <input type="url" name="link" x-model="link"
                               class="input-form"
                               placeholder="https://drive.google.com/..."
                               autocomplete="off">
                        <p class="text-xs text-slate-400 mt-1.5">Link ini hanya berlaku untuk tahun <span class="font-semibold">{{ $tahun }}</span>.</p>
                    </div>

                    {{-- Preview link jika ada --}}
                    <template x-if="link">
                        <div class="flex items-center gap-2 p-3 bg-teal-50 border border-teal-200 rounded-lg">
                            <svg class="w-4 h-4 text-teal-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                            <a :href="link" target="_blank" rel="noopener noreferrer"
                               class="text-xs text-teal-700 hover:text-teal-800 font-medium truncate"
                               x-text="link"></a>
                        </div>
                    </template>

                    <div class="modal-footer justify-end">
                        <button type="button" @click="open = false" class="btn-secondary">Batal</button>
                        <button type="submit" class="btn-primary" style="background:#0d9488;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>

</div>
@endpush
