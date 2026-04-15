@extends('layout.dashboard')

@section('title', 'Profil Saya - SIPS - Sistem Informasi Pengaduan Sekolah')

@section('content')
<div class="py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Profil Saya</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola informasi akun dan keamanan</p>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Avatar & Stats --}}
        <div class="space-y-6">
            {{-- Avatar Card --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                <div class="text-center mb-5">
                    <div class="relative inline-block">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-2xl object-cover shadow-md mx-auto" id="avatar-preview">
                        <label for="avatar-upload" class="absolute -bottom-2 -right-2 w-8 h-8 rounded-xl flex items-center justify-center cursor-pointer shadow-md hover:opacity-90 transition-opacity" style="background: linear-gradient(135deg, #cc2c6b, #374151);">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </label>
                    </div>
                    <h2 class="font-bold text-gray-900 mt-3">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                </div>

                <form action="{{ route('profile.update-avatar') }}" method="POST" enctype="multipart/form-data" id="avatar-form">
                    @csrf
                    <input type="file" id="avatar-upload" name="avatar" accept="image/*" class="hidden" onchange="previewAndSubmit(this)">
                </form>

                @if($user->avatar)
                <form action="{{ route('profile.remove-avatar') }}" method="POST" onsubmit="return confirm('Hapus foto profil?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full py-2 text-xs font-semibold text-red-500 hover:text-red-700 transition-colors">Hapus foto profil</button>
                </form>
                @endif
            </div>

            {{-- Stats --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 mb-4">Statistik Saya</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Total Pengaduan</span>
                        <span class="font-bold text-gray-900">{{ $stats['total_pengaduan'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Pending</span>
                        <span class="font-bold text-yellow-600">{{ $stats['pending_pengaduan'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Selesai</span>
                        <span class="font-bold text-green-600">{{ $stats['resolved_pengaduan'] }}</span>
                    </div>
                    @if($user->isPetugas())
                    <div class="pt-3 mt-3 border-t border-gray-100 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Ditugaskan</span>
                            <span class="font-bold text-blue-600">{{ $stats['assigned_total'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Masih Aktif</span>
                            <span class="font-bold text-purple-600">{{ $stats['assigned_pending'] ?? 0 }}</span>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100 flex gap-2">
                    <a href="{{ route('profile.activity') }}" class="flex-1 py-2 text-center text-xs font-semibold text-gray-600 bg-gray-50 border border-gray-100 rounded-xl hover:bg-gray-100 transition-colors">Aktivitas</a>
                    <a href="{{ route('profile.export-data') }}" class="flex-1 py-2 text-center text-xs font-semibold text-pink-600 bg-pink-50 border border-pink-100 rounded-xl hover:bg-pink-100 transition-colors">Export Data</a>
                </div>
            </div>
        </div>

        {{-- Right: Forms --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Update Profile --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                <h2 class="text-base font-bold text-gray-900 mb-5">Informasi Profil</h2>
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PATCH')
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300">
                                @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300">
                                @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Telepon</label>
                            <input type="text" name="telp" value="{{ old('telp', $user->telp) }}" placeholder="08xxxxxxxx" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat</label>
                            <textarea name="alamat" rows="2" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300 resize-none" placeholder="Alamat lengkap...">{{ old('alamat', $user->alamat) }}</textarea>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-500 bg-gray-50 p-3 rounded-xl">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Role: <strong>{{ ucfirst(str_replace('_', ' ', $user->role)) }}</string>
                            @if($user->department) · Departemen: <strong>{{ $user->department }}</strong>@endif
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="px-6 py-2.5 text-sm text-white font-bold rounded-xl transition-all hover:-translate-y-0.5 hover:shadow-lg" style="background: linear-gradient(135deg, #cc2c6b, #374151);">Simpan Profil</button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Update Password --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                <h2 class="text-base font-bold text-gray-900 mb-5">Ubah Password</h2>
                <form action="{{ route('profile.update-password') }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Saat Ini <span class="text-red-500">*</span></label>
                            <input type="password" name="current_password" required class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300">
                            @error('current_password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Baru <span class="text-red-500">*</span></label>
                                <input type="password" name="password" required class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300">
                                @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password <span class="text-red-500">*</span></label>
                                <input type="password" name="password_confirmation" required class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300">
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="px-6 py-2.5 text-sm text-white font-bold rounded-xl transition-all hover:-translate-y-0.5 hover:shadow-lg" style="background: linear-gradient(135deg, #3b82f6, #1e40af);">Ubah Password</button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Danger Zone --}}
            <div class="bg-red-50 border border-red-100 rounded-2xl p-6">
                <h2 class="text-base font-bold text-red-700 mb-2">Zona Berbahaya</h2>
                <p class="text-sm text-red-600 mb-4">Sekali akun dihapus, semua data tidak dapat dikembalikan.</p>
                <button type="button" onclick="document.getElementById('delete-modal').classList.remove('hidden')" class="px-5 py-2 text-sm font-semibold text-red-600 bg-white border border-red-200 rounded-xl hover:bg-red-100 transition-colors">
                    Hapus Akun Saya
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Delete Modal --}}
<div id="delete-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
    <div class="bg-white rounded-2xl p-6 shadow-2xl max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus Akun</h3>
        <p class="text-sm text-gray-500 mb-5">Masukkan password Anda untuk konfirmasi penghapusan akun.</p>
        <form action="{{ route('profile.destroy') }}" method="POST">
            @csrf @method('DELETE')
            <div class="mb-4">
                <input type="password" name="password" required placeholder="Masukkan password..." class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-300">
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('delete-modal').classList.add('hidden')" class="flex-1 py-2.5 text-sm font-semibold text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">Batal</button>
                <button type="submit" class="flex-1 py-2.5 text-sm font-semibold text-white bg-red-500 rounded-xl hover:bg-red-600">Hapus Akun</button>
            </div>
        </form>
    </div>
</div>

<script>
function previewAndSubmit(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatar-preview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
        setTimeout(() => document.getElementById('avatar-form').submit(), 300);
    }
}
</script>
@endsection

