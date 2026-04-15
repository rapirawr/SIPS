@extends('layout.dashboard')

@section('title', 'Detail User - ' . $user->name)

@section('content')
<div class="py-8">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('users.index') }}" class="p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900">Detail User</h1>
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
        {{-- User Profile Card --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm text-center">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-2xl object-cover mx-auto mb-4 shadow-md">
                <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $user->email }}</p>

                @php
                $roleBadge = ['admin' => 'bg-pink-100 text-pink-700','kepala_sekolah' => 'bg-purple-100 text-purple-700','koordinator' => 'bg-blue-100 text-blue-700','petugas' => 'bg-cyan-100 text-cyan-700','user' => 'bg-gray-100 text-gray-600'];
                $roleLabel = ['admin' => 'Admin','kepala_sekolah' => 'Kepala Sekolah','koordinator' => 'Koordinator','petugas' => 'Petugas','user' => 'User'];
                @endphp
                <div class="flex items-center justify-center gap-2 mt-3">
                    <span class="px-3 py-1 rounded-xl text-sm font-semibold {{ $roleBadge[$user->role] ?? 'bg-gray-100 text-gray-600' }}">{{ $roleLabel[$user->role] ?? $user->role }}</span>
                    <span class="px-3 py-1 rounded-xl text-sm font-semibold {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                </div>

                <div class="flex gap-2 mt-5">
                    <a href="{{ route('users.edit', $user) }}" class="flex-1 py-2 text-sm font-semibold text-blue-600 bg-blue-50 border border-blue-100 rounded-xl hover:bg-blue-100 transition-colors text-center">Edit</a>
                    <form action="{{ route('users.toggle-active', $user) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full py-2 text-sm font-semibold {{ $user->is_active ? 'text-yellow-600 bg-yellow-50 border-yellow-100' : 'text-green-600 bg-green-50 border-green-100' }} border rounded-xl hover:opacity-80 transition-opacity">
                            {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- Info --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 mb-4">Informasi Akun</h3>
                <div class="space-y-3 text-sm">
                    @if($user->department)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Departemen</span>
                        <span class="font-semibold text-gray-800">{{ $user->department }}</span>
                    </div>
                    @endif
                    @if($user->telp)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Telepon</span>
                        <span class="font-semibold text-gray-800">{{ $user->telp }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Bergabung</span>
                        <span class="font-semibold text-gray-800">{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Notifikasi</span>
                        <span class="font-semibold text-gray-800">{{ $user->unread_notifications_count }} belum dibaca</span>
                    </div>
                </div>
            </div>

            {{-- Reset Password --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 mb-4">Reset Password</h3>
                <form action="{{ route('users.reset-password', $user) }}" method="POST">
                    @csrf
                    <div class="space-y-3">
                        <input type="password" name="password" placeholder="Password baru" required class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300">
                        <input type="password" name="password_confirmation" placeholder="Konfirmasi password" required class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300">
                        <button type="submit" class="w-full py-2.5 text-sm text-white font-semibold rounded-xl" style="background: linear-gradient(135deg, #cc2c6b, #374151);">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_pengaduan'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Total Laporan</p>
                </div>
                <div class="bg-white rounded-2xl border border-green-100 p-4 shadow-sm text-center border-l-4 border-l-green-400">
                    <p class="text-2xl font-bold text-green-600">{{ $stats['pengaduan_resolved'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Laporan Selesai</p>
                </div>
                <div class="bg-white rounded-2xl border border-blue-100 p-4 shadow-sm text-center border-l-4 border-l-blue-400">
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['assigned_total'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Total Ditugaskan</p>
                </div>
                <div class="bg-white rounded-2xl border border-yellow-100 p-4 shadow-sm text-center border-l-4 border-l-yellow-400">
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['assigned_pending'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Masih Aktif</p>
                </div>
            </div>

            {{-- Pengaduan Dibuat --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900">Pengaduan Dibuat</h3>
                    <span class="text-xs text-gray-400">{{ $stats['total_pengaduan'] }} total</span>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($user->pengaduan as $p)
                    <a href="{{ route('pengaduan.show', $p->kode_unik) }}" class="flex items-center justify-between px-6 py-3.5 hover:bg-gray-50 transition-colors">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $p->judul }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $p->kode_unik }} · {{ $p->created_at->format('d M Y') }}</p>
                        </div>
                        <span class="ml-4 flex-shrink-0 px-2.5 py-1 rounded-lg text-xs font-semibold {{ $p->status_badge['class'] }}">{{ $p->status_badge['text'] }}</span>
                    </a>
                    @empty
                    <div class="px-6 py-8 text-center text-sm text-gray-400">Belum membuat pengaduan</div>
                    @endforelse
                </div>
            </div>

            {{-- Pengaduan Ditugaskan --}}
            @if($user->isPetugas())
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900">Pengaduan Ditugaskan</h3>
                    <a href="{{ route('users.performance', $user) }}" class="text-xs text-pink-600 font-semibold hover:underline">Lihat Performa →</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($user->pengaduanAssigned as $p)
                    <a href="{{ route('pengaduan.show', $p->kode_unik) }}" class="flex items-center justify-between px-6 py-3.5 hover:bg-gray-50 transition-colors">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $p->judul }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $p->kode_unik }} · {{ $p->created_at->format('d M Y') }}</p>
                        </div>
                        <span class="ml-4 flex-shrink-0 px-2.5 py-1 rounded-lg text-xs font-semibold {{ $p->status_badge['class'] }}">{{ $p->status_badge['text'] }}</span>
                    </a>
                    @empty
                    <div class="px-6 py-8 text-center text-sm text-gray-400">Belum ada pengaduan yang ditugaskan</div>
                    @endforelse
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

