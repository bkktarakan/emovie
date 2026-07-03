@extends('layouts.app')
@section('title', 'Output')

@section('actions')
    <button @click="$dispatch('open-modal', 'import-output')" class="btn-secondary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
        Import Excel
    </button>
    <button @click="$dispatch('open-modal', 'tambah-output')" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Output
    </button>
@endsection

@section('content')
<div class="card" x-data="{ search: '' }">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-4">
        <div>
            <h2 class="font-semibold text-slate-800">Daftar Output</h2>
            <p class="text-xs text-slate-400 mt-0.5">Tahun {{ $tahun }} &mdash; {{ $outputs->count() }} output terdaftar</p>
        </div>
        <div class="relative w-64">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" x-model="search" placeholder="Cari kode atau nama output..."
                   class="w-full pl-9 pr-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-10">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Kode Output</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Output</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Volume</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Satuan</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Pagu Anggaran</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">% Realisasi</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider w-20">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($outputs as $i => $output)
                <tr class="hover:bg-blue-50/40 transition-colors group"
                    x-show="search === '' || '{{ strtolower($output->kode_output) }} {{ strtolower($output->nama_output) }}'.includes(search.toLowerCase())"
                    x-cloak>
                    <td class="px-4 py-3.5 text-slate-400 text-xs font-medium">{{ $i + 1 }}</td>
                    <td class="px-4 py-3.5">
                        <span class="font-mono text-xs text-blue-700 bg-blue-50 px-2 py-1 rounded-md font-semibold">{{ $output->kode_output }}</span>
                    </td>
                    <td class="px-4 py-3.5 text-slate-700 max-w-xs">
                        <span title="{{ $output->nama_output }}" class="line-clamp-2 leading-snug">{{ $output->nama_output }}</span>
                    </td>
                    <td class="px-4 py-3.5 text-right text-slate-800 font-semibold">{{ number_format($output->volume) }}</td>
                    <td class="px-4 py-3.5 text-slate-500 text-xs font-medium">{{ $output->satuan }}</td>
                    <td class="px-4 py-3.5 text-right text-slate-700 font-medium">Rp {{ number_format($output->anggaran, 0, ',', '.') }}</td>
                    <td class="px-4 py-3.5">
                        @php $pct = $output->akumulatif?->persentase_volume ?? 0; @endphp
                        <div class="flex items-center gap-2 min-w-[100px]">
                            <div class="flex-1 bg-slate-100 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all {{ $pct >= 80 ? 'bg-emerald-500' : ($pct >= 50 ? 'bg-amber-400' : 'bg-red-400') }}"
                                     style="width: {{ min($pct, 100) }}%"></div>
                            </div>
                            <span class="text-xs font-bold {{ $pct >= 80 ? 'text-emerald-600' : ($pct >= 50 ? 'text-amber-600' : 'text-red-500') }} w-12 text-right">{{ number_format($pct, 2) }}%</span>
                        </div>
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-center gap-1">
                            <button @click="$dispatch('open-modal', 'edit-output-{{ $output->id }}')"
                                    class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <form method="POST" action="{{ route('output.destroy', $output->id) }}"
                                  onsubmit="return confirm('Hapus output {{ addslashes($output->kode_output) }}?\nSemua data realisasi akan ikut terhapus.')"
                                  class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center gap-3 text-slate-400">
                            <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center">
                                <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <p class="font-semibold text-slate-500">Belum ada output</p>
                            <p class="text-sm">Klik tombol "Tambah Output" untuk menambahkan data.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

{{-- ===== MODALS (di luar tabel agar HTML valid) ===== --}}

{{-- Import Excel Modal --}}
@push('modals')
<div x-data="{ open: false }" @open-modal.window="open = $event.detail === 'import-output'"
     x-show="open" x-cloak class="modal-overlay"
     x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="modal-backdrop" @click="open = false"></div>
    <div class="modal-box max-w-md"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         @click.stop>
        <div class="modal-header">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                </div>
                <h3 class="font-semibold text-slate-800">Import Output dari Excel</h3>
            </div>
            <button @click="open = false" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('output.import') }}" enctype="multipart/form-data" class="modal-body">
            @csrf
            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-700">
                File harus menggunakan format kolom:
                <strong>kode_output, nama_output, volume, satuan, anggaran</strong>.
                <a href="{{ route('output.template') }}" class="underline font-semibold ml-1">Download template →</a>
            </div>
            <div>
                <label class="label-form">Pilih File <span class="text-red-500">*</span></label>
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                       class="input-form file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-xs text-slate-400 mt-1">Format: .xlsx, .xls, atau .csv — Maks 2MB</p>
            </div>
            <div class="modal-footer">
                <button type="button" @click="open = false" class="btn-secondary flex-1">Batal</button>
                <button type="submit" class="btn-primary flex-1 justify-center" style="background:#059669;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Import Sekarang
                </button>
            </div>
        </form>
    </div>
</div>
@endpush

{{-- Tambah Output Modal --}}
@push('modals')
<div x-data="{ open: false }" @open-modal.window="open = $event.detail === 'tambah-output'"
     x-show="open" x-cloak class="modal-overlay"
     x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="modal-backdrop" @click="open = false"></div>
    <div class="modal-box max-w-lg"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         @click.stop>
        <div class="modal-header">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <h3 class="font-semibold text-slate-800">Tambah Output Baru</h3>
            </div>
            <button @click="open = false" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('output.store') }}" class="modal-body">
            @csrf
            <div>
                <label class="label-form">Kode Output <span class="text-red-500">*</span></label>
                <input type="text" name="kode_output" value="{{ old('kode_output') }}" placeholder="contoh: 4249.PEA.001.051" required class="input-form">
                <p class="mt-1 text-xs text-slate-400">Hanya huruf, angka, titik, dan tanda hubung.</p>
            </div>
            <div>
                <label class="label-form">Nama Output <span class="text-red-500">*</span></label>
                <textarea name="nama_output" rows="2" placeholder="Deskripsi nama output..." required class="input-form">{{ old('nama_output') }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label-form">Volume <span class="text-red-500">*</span></label>
                    <input type="number" name="volume" value="{{ old('volume') }}" min="1" placeholder="0" required class="input-form">
                </div>
                <div>
                    <label class="label-form">Satuan <span class="text-red-500">*</span></label>
                    <select name="satuan" required class="input-form">
                        @foreach(\App\Models\Output::getSatuanOptions() as $s)
                            <option value="{{ $s }}" {{ old('satuan') === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="label-form">Pagu Anggaran (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="anggaran" value="{{ old('anggaran') }}" min="0" placeholder="0" required class="input-form">
            </div>
            <div class="modal-footer">
                <button type="button" @click="open = false" class="btn-secondary flex-1">Batal</button>
                <button type="submit" class="btn-primary flex-1 justify-center">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endpush

{{-- Edit Output Modals (per row) --}}
@foreach($outputs as $output)
@push('modals')
<div x-data="{ open: false }" @open-modal.window="open = $event.detail === 'edit-output-{{ $output->id }}'"
     x-show="open" x-cloak class="modal-overlay"
     x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="modal-backdrop" @click="open = false"></div>
    <div class="modal-box max-w-lg"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         @click.stop>
        <div class="modal-header">
            <div>
                <h3 class="font-semibold text-slate-800">Edit Output</h3>
                <p class="text-xs text-slate-400 mt-0.5 font-mono">{{ $output->kode_output }}</p>
            </div>
            <button @click="open = false" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('output.update', $output->id) }}" class="modal-body">
            @csrf @method('PUT')
            <div>
                <label class="label-form">Nama Output <span class="text-red-500">*</span></label>
                <textarea name="nama_output" rows="2" required class="input-form">{{ $output->nama_output }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label-form">Volume <span class="text-red-500">*</span></label>
                    <input type="number" name="volume" value="{{ $output->volume }}" min="1" required class="input-form">
                </div>
                <div>
                    <label class="label-form">Satuan <span class="text-red-500">*</span></label>
                    <select name="satuan" required class="input-form">
                        @foreach(\App\Models\Output::getSatuanOptions() as $s)
                            <option value="{{ $s }}" {{ $output->satuan === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="label-form">Pagu Anggaran (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="anggaran" value="{{ $output->anggaran }}" min="0" required class="input-form">
                <p class="mt-1 text-xs text-amber-600">Mengubah volume/anggaran akan merekalkukasi semua persentase realisasi.</p>
            </div>
            <div class="modal-footer">
                <button type="button" @click="open = false" class="btn-secondary flex-1">Batal</button>
                <button type="submit" class="btn-primary flex-1 justify-center">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endpush
@endforeach
