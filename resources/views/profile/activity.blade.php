@extends('layout.dashboard')

@section('title', 'Aktivitas Saya - SIPS - Sarana Informasi Pengaduan Sekolah')

@section('content')
<div class="py-8">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('profile.edit') }}" class="p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900">Aktivitas Saya</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Pengaduan Saya --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-bold text-gray-900">Pengaduan Saya</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentPengaduan as $p)
                <a href="{{ route('pengaduan.show', $p->kode_unik) }}" class="flex items-start gap-3 px-6 py-4 hover:bg-gray-50 transition-colors">
                    <span class="mt-0.5 px-2 py-0.5 text-xs font-semibold rounded-lg flex-shrink-0 {{ $p->status_badge['class'] }}">{{ $p->status_badge['text'] }}</span>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $p->judul }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $p->kategori->nama ?? '-' }} · {{ $p->created_at->format('d M Y') }}</p>
                    </div>
                </a>
                @empty
                <div class="px-6 py-10 text-center">
                    <p class="text-sm text-gray-400">Belum ada pengaduan</p>
                    <a href="{{ route('pengaduan.create') }}" class="mt-2 inline-block text-xs text-pink-600 font-semibold hover:underline">Buat pengaduan →</a>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Pengaduan Ditugaskan (Petugas only) --}}
        @if($assignedPengaduan)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-bold text-gray-900">Pengaduan Ditugaskan</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($assignedPengaduan as $p)
                <a href="{{ route('pengaduan.show', $p->kode_unik) }}" class="flex items-start gap-3 px-6 py-4 hover:bg-gray-50 transition-colors">
                    <span class="mt-0.5 px-2 py-0.5 text-xs font-semibold rounded-lg flex-shrink-0 {{ $p->status_badge['class'] }}">{{ $p->status_badge['text'] }}</span>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $p->judul }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $p->kategori->nama ?? '-' }} · {{ $p->created_at->format('d M Y') }}</p>
                    </div>
                </a>
                @empty
                <div class="px-6 py-10 text-center">
                    <p class="text-sm text-gray-400">Belum ada pengaduan ditugaskan</p>
                </div>
                @endforelse
            </div>
        </div>
        @endif

        {{-- Notifikasi --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden {{ $assignedPengaduan ? 'lg:col-span-2' : '' }}">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-bold text-gray-900">Notifikasi Terbaru</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($notifications as $notif)
                <div class="flex items-start gap-3 px-6 py-4 {{ $notif->is_read ? 'opacity-60' : '' }}">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5
                        @if($notif->tipe === 'success') bg-green-100
                        @elseif($notif->tipe === 'warning') bg-yellow-100
                        @elseif($notif->tipe === 'error') bg-red-100
                        @else bg-blue-100
                        @endif">
                        @if($notif->tipe === 'success')
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                        @elseif($notif->tipe === 'warning')
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                        @else
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900">{{ $notif->judul }}</p>
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ $notif->pesan }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                    </div>
                    @if(!$notif->is_read)
                    <span class="w-2 h-2 rounded-full bg-pink-500 flex-shrink-0 mt-2"></span>
                    @endif
                </div>
                @empty
                <div class="px-6 py-10 text-center">
                    <p class="text-sm text-gray-400">Belum ada notifikasi</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
