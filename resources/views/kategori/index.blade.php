@extends('layout.dashboard')

@section('title', 'Kategori Pengaduan - SIPS - Sistem Informasi Pengaduan Sekolah')

@section('content')
<div class="py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kategori Pengaduan</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola kategori dan batas waktu penanganan pengaduan</p>
        </div>
        <a href="{{ route('kategori.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-white text-sm font-bold rounded-xl transition-all hover:-translate-y-0.5 hover:shadow-lg" style="background: linear-gradient(135deg, #cc2c6b, #374151);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Kategori
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
    </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($kategoris as $k)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow group">
            {{-- Color bar --}}
            <div class="h-1.5" style="background-color: {{ $k->warna ?? '#cc2c6b' }}"></div>

            <div class="p-5">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 text-xl" style="background-color: {{ $k->warna ?? '#cc2c6b' }}20">
                            {{ $k->icon ?? '??' }}
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-bold text-gray-900 truncate">{{ $k->nama }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Urutan {{ $k->urutan ?? '-' }}</p>
                        </div>
                    </div>
                    <span class="flex-shrink-0 px-2.5 py-1 text-xs font-semibold rounded-lg {{ $k->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                        {{ $k->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                @if($k->deskripsi)
                <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $k->deskripsi }}</p>
                @endif

                <div class="grid grid-cols-3 gap-3 mb-4">
                    <div class="text-center p-2.5 bg-gray-50 rounded-xl">
                        <p class="text-lg font-bold text-gray-900">{{ $k->pengaduan_count }}</p>
                        <p class="text-xs text-gray-500">Total</p>
                    </div>
                    <div class="text-center p-2.5 bg-gray-50 rounded-xl">
                        <p class="text-lg font-bold text-gray-900">{{ $k->sla_hours }}</p>
                        <p class="text-xs text-gray-500">Batas Waktu (jam)</p>
                    </div>
                    <div class="text-center p-2.5 bg-gray-50 rounded-xl">
                        @if($k->picDefault)
                        <p class="text-xs font-bold text-gray-700 truncate">{{ explode(' ', $k->picDefault->name)[0] }}</p>
                        @else
                        <p class="text-xs text-gray-400">Tak ada</p>
                        @endif
                        <p class="text-xs text-gray-500">PIC</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('kategori.show', $k) }}" class="flex-1 py-2 text-center text-xs font-semibold text-gray-600 bg-gray-50 border border-gray-100 rounded-xl hover:bg-gray-100 transition-colors">Detail</a>
                    <a href="{{ route('kategori.edit', $k) }}" class="flex-1 py-2 text-center text-xs font-semibold text-blue-600 bg-blue-50 border border-blue-100 rounded-xl hover:bg-blue-100 transition-colors">Edit</a>
                    <form action="{{ route('kategori.toggle-active', $k) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full py-2 text-xs font-semibold {{ $k->is_active ? 'text-yellow-600 bg-yellow-50 border-yellow-100' : 'text-green-600 bg-green-50 border-green-100' }} border rounded-xl hover:opacity-80 transition-opacity">
                            {{ $k->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 py-16 text-center">
            <svg class="w-14 h-14 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            <p class="text-gray-400 font-medium">Belum ada kategori</p>
            <a href="{{ route('kategori.create') }}" class="mt-3 inline-block text-sm text-pink-600 font-semibold hover:underline">Buat kategori pertama ?</a>
        </div>
        @endforelse
    </div>
</div>
@endsection

