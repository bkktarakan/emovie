@extends('layouts.app')
@section('title', 'Manajemen Pengguna')

@section('actions')
    <button @click="$dispatch('open-modal', 'tambah-user')" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Pengguna
    </button>
@endsection

@section('content')
<div class="card" x-data="{}">
    <div class="px-6 py-4 border-b border-slate-100">
        <h2 class="font-semibold text-slate-800">Daftar Pengguna</h2>
        <p class="text-xs text-slate-400 mt-0.5">{{ $users->count() }} pengguna terdaftar</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-10">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Username</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Level</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider w-24">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users as $i => $user)
                @php $isSelf = $user->id === session('user_id'); @endphp
                <tr class="hover:bg-blue-50/40 transition-colors {{ $isSelf ? 'bg-blue-50/30' : '' }}">
                    <td class="px-4 py-3.5 text-slate-400 text-xs font-medium">{{ $i + 1 }}</td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0
                                {{ $user->level === 'admin' ? 'bg-red-500 ring-2 ring-red-200' : 'bg-blue-500 ring-2 ring-blue-200' }}">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800">{{ $user->name }}</p>
                                @if($isSelf)
                                    <span class="text-xs text-blue-600 font-medium">(Akun Anda)</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="font-mono text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded-md">{{ $user->username }}</span>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="status-badge border {{ $user->level === 'admin' ? 'bg-red-100 text-red-700 border-red-200' : 'bg-slate-100 text-slate-600 border-slate-200' }}">
                            {{ ucfirst($user->level) }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <div class="flex items-center justify-center gap-1">
                            <button @click="$dispatch('open-modal', 'edit-user-{{ $user->id }}')"
                                    class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            @if(!$isSelf)
                            <form method="POST" action="{{ route('users.destroy', $user->id) }}"
                                  onsubmit="return confirm('Hapus pengguna {{ addslashes($user->name) }}?\nAksi ini tidak dapat dibatalkan.')"
                                  class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                            @else
                            <div class="w-7"></div>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                        <p class="font-medium">Belum ada pengguna terdaftar.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

{{-- ===== MODALS ===== --}}

{{-- Tambah User Modal --}}
@push('modals')
<div x-data="{ open: false, showPass: false }" @open-modal.window="open = $event.detail === 'tambah-user'"
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
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <h3 class="font-semibold text-slate-800">Tambah Pengguna</h3>
            </div>
            <button @click="open = false" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('users.store') }}" class="modal-body">
            @csrf
            <div>
                <label class="label-form">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required class="input-form" placeholder="Nama lengkap pengguna">
            </div>
            <div>
                <label class="label-form">Username <span class="text-red-500">*</span></label>
                <input type="text" name="username" value="{{ old('username') }}" required class="input-form" placeholder="huruf, angka, underscore">
            </div>
            <div>
                <label class="label-form">Password <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input :type="showPass ? 'text' : 'password'" name="password" required
                           class="input-form pr-10" placeholder="Min. 8 karakter, huruf+angka+simbol">
                    <button type="button" @click="showPass = !showPass" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
            </div>
            <div>
                <label class="label-form">Level <span class="text-red-500">*</span></label>
                <select name="level" class="input-form">
                    <option value="operator">Operator</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" @click="open = false" class="btn-secondary flex-1">Batal</button>
                <button type="submit" class="btn-primary flex-1 justify-center">Tambah Pengguna</button>
            </div>
        </form>
    </div>
</div>
@endpush

{{-- Edit User Modals (per row) --}}
@foreach($users as $user)
@push('modals')
<div x-data="{ open: false }" @open-modal.window="open = $event.detail === 'edit-user-{{ $user->id }}'"
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
                <h3 class="font-semibold text-slate-800">Edit Pengguna</h3>
                <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $user->username }}</p>
            </div>
            <button @click="open = false" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('users.update', $user->id) }}" class="modal-body">
            @csrf @method('PUT')
            <div>
                <label class="label-form">Nama Lengkap</label>
                <input type="text" name="name" value="{{ $user->name }}" required class="input-form">
            </div>
            <div>
                <label class="label-form">Level</label>
                <select name="level" class="input-form">
                    <option value="admin" {{ $user->level === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="operator" {{ $user->level === 'operator' ? 'selected' : '' }}>Operator</option>
                </select>
            </div>
            <div>
                <label class="label-form">Password Baru <span class="text-slate-400 font-normal">(kosongkan jika tidak diganti)</span></label>
                <input type="password" name="password" class="input-form" placeholder="Min. 8 karakter dengan huruf, angka, simbol">
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
