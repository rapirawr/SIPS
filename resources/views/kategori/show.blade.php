@extends('layout.dashboard')

@section('title', 'Detail Kategori - ' . $kategori->nama)

@section('content')
<div class="py-8">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('kategori.index') }}" class="p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900">Detail Kategori</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Info --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="h-2" style="background-color: {{ $kategori->warna ?? '#cc2c6b' }}"></div>
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl" style="background-color: {{ $kategori->warna ?? '#cc2c6b' }}20">
                            {{ $kategori->icon ?? '📋' }}
                        </div>
                        <div>
                            <h2 class="font-bold text-gray-900 text-lg">{{ $kategori->nama }}</h2>
                            <span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-lg {{ $kategori->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">{{ $kategori->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </div>
                    </div>

                    @if($kategori->deskripsi)
                    <p class="text-sm text-gray-600 mb-4">{{ $kategori->deskripsi }}</p>
                    @endif

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Target Waktu Penanganan</span>
                            <span class="font-semibold text-gray-800">{{ $kategori->sla_hours }} jam</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Slug</span>
                            <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded-lg">{{ $kategori->slug }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Urutan</span>
                            <span class="font-semibold text-gray-800">{{ $kategori->urutan ?? '-' }}</span>
                        </div>
                        @if($kategori->picDefault)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">PIC Default</span>
                            <div class="flex items-center gap-2">
                                <img src="{{ $kategori->picDefault->avatar_url }}" class="w-6 h-6 rounded-full" alt="">
                                <span class="font-semibold text-gray-800 text-xs">{{ $kategori->picDefault->name }}</span>
                            </div>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-500">Warna</span>
                            <div class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded-full border border-gray-200" style="background-color: {{ $kategori->warna ?? '#cc2c6b' }}"></span>
                                <span class="font-mono text-xs text-gray-600">{{ $kategori->warna ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2 mt-5">
                        <a href="{{ route('kategori.edit', $kategori) }}" class="flex-1 py-2 text-center text-sm font-semibold text-blue-600 bg-blue-50 border border-blue-100 rounded-xl hover:bg-blue-100 transition-colors">Edit</a>
                        <form action="{{ route('kategori.toggle-active', $kategori) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full py-2 text-sm font-semibold {{ $kategori->is_active ? 'text-yellow-600 bg-yellow-50 border-yellow-100' : 'text-green-600 bg-green-50 border-green-100' }} border rounded-xl hover:opacity-80 transition-opacity">
                                {{ $kategori->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                    </div>

                    @if($stats['total'] === 0)
                    <form action="{{ route('kategori.destroy', $kategori) }}" method="POST" class="mt-2" onsubmit="return confirm('Yakin hapus kategori ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full py-2 text-sm font-semibold text-red-600 bg-red-50 border border-red-100 rounded-xl hover:bg-red-100 transition-colors">Hapus Kategori</button>
                    </form>
                    @endif
                </div>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-white rounded-xl border border-gray-100 p-3 text-center shadow-sm">
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Total</p>
                </div>
                <div class="bg-yellow-50 rounded-xl border border-yellow-100 p-3 text-center shadow-sm">
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Pending</p>
                </div>
                <div class="bg-green-50 rounded-xl border border-green-100 p-3 text-center shadow-sm">
                    <p class="text-2xl font-bold text-green-600">{{ $stats['resolved'] }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Selesai</p>
                </div>
            </div>
        </div>

        {{-- Right: Recent Pengaduan --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900">Pengaduan Terbaru</h3>
                    <a href="{{ route('pengaduan.index', ['kategori_id' => $kategori->id]) }}" class="text-xs text-pink-600 font-semibold hover:underline">Lihat Semua →</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($kategori->pengaduan as $p)
                    <a href="{{ route('pengaduan.show', $p->kode_unik) }}" class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition-colors">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $p->judul }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $p->kode_unik }} · {{ $p->created_at->format('d M Y') }}</p>
                        </div>
                        <div class="ml-4 flex items-center gap-2 flex-shrink-0">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-semibold {{ $p->urgensi_badge['class'] }}">{{ $p->urgensi_badge['text'] }}</span>
                            <span class="px-2.5 py-1 rounded-lg text-xs font-semibold {{ $p->status_badge['class'] }}">{{ $p->status_badge['text'] }}</span>
                        </div>
                    </a>
                    @empty
                    <div class="px-6 py-12 text-center">
                        <p class="text-sm text-gray-400">Belum ada pengaduan untuk kategori ini</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

