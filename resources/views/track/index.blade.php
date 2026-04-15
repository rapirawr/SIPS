
@extends('layout.dashboard')

@section('title', 'Lacak Pengaduan - SIPS - Sarana Informasi Pengaduan Sekolah')

@section('content')



    <div class="py-8 flex flex-col items-center">
        {{-- Logo & Header --}}
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center gap-3 mb-6">
                <img src="{{ asset('storage/asset/img/logo.webp') }}" class="h-10" alt="Logo" onerror="this.style.display='none'">
                <span class="text-lg font-bold text-gray-800">SIPS - Sarana Informasi Pengaduan Sekolah</span>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Lacak Pengaduan</h1>
            <p class="text-gray-500 mt-2 text-sm">Masukkan kode unik untuk melihat status pengaduan Anda</p>
        </div>

        {{-- Search Form --}}
        <div class="w-full max-w-md mb-8">
            <form action="{{ route('pengaduan.track.search') }}" method="POST" class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                @csrf
                @if(session('error'))
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('error') }}
                </div>
                @endif

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kode Pengaduan</label>
                    <input type="text" name="kode_unik" value="{{ old('kode_unik') }}" required
                           placeholder="Contoh: PKS-20260404-ABCXYZ"
                           class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300 font-mono tracking-wide">
                    @error('kode_unik')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="w-full py-3 text-white font-bold rounded-xl transition-all hover:-translate-y-0.5 hover:shadow-lg text-sm" style="background: linear-gradient(135deg, #cc2c6b, #374151);">
                    Lacak Sekarang
                </button>
            </form>

            <!-- <div class="mt-4 text-center">
                <a href="/" class="text-sm text-gray-400 hover:text-pink-600 transition-colors">← Kembali ke Beranda</a>
            </div> -->
        </div>

        {{-- Result --}}
        @if(isset($pengaduan))
        <div class="w-full max-w-2xl">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="h-2" style="background: linear-gradient(135deg, #cc2c6b, #374151)"></div>

                <div class="p-6">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                        <div>
                            <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Kode Pengaduan</p>
                            <p class="font-mono text-lg font-bold text-gray-900">{{ $pengaduan->kode_unik }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1.5 rounded-xl text-sm font-bold {{ $pengaduan->status_badge['class'] }}">
                                {{ $pengaduan->status_badge['text'] }}
                            </span>
                            <span class="px-3 py-1.5 rounded-xl text-sm font-semibold {{ $pengaduan->urgensi_badge['class'] }}">
                                {{ $pengaduan->urgensi_badge['text'] }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-5 pb-5 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900 mb-2">{{ $pengaduan->judul }}</h2>
                        @if($pengaduan->kategori)
                        <p class="text-sm text-gray-500">
                            <span class="font-medium">Kategori:</span> {{ $pengaduan->kategori->nama }}
                        </p>
                        @endif
                        <p class="text-sm text-gray-500 mt-1">
                            <span class="font-medium">Dilaporkan:</span> {{ $pengaduan->created_at->format('d M Y, H:i') }}
                        </p>
                        @if($pengaduan->lokasi_kejadian)
                        <p class="text-sm text-gray-500 mt-1">
                            <span class="font-medium text-gray-400">Lokasi:</span> {{ $pengaduan->lokasi_kejadian }}
                        </p>
                        @endif
                        @if($pengaduan->assignedUser)
                        <p class="text-sm text-gray-500 mt-1">
                            <span class="font-medium text-gray-400">Petugas:</span> {{ $pengaduan->assignedUser->name }}
                        </p>
                        @endif
                    </div>

                    <div class="mb-5 pb-5 border-b border-gray-100">
                        <p class="text-xs text-gray-400 font-medium uppercase mb-2">Deskripsi Laporan</p>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $pengaduan->deskripsi }}</p>

                        @if($pengaduan->bukti_foto && count($pengaduan->bukti_foto) > 0)
                        <div class="mt-4 grid grid-cols-3 sm:grid-cols-4 gap-2">
                            @foreach($pengaduan->bukti_foto as $foto)
                            <a href="{{ asset('storage/' . $foto) }}" target="_blank" class="block rounded-lg overflow-hidden border border-gray-100">
                                <img src="{{ asset('storage/' . $foto) }}" class="w-full h-16 object-cover" alt="Bukti">
                            </a>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    {{-- Batas Waktu Penanganan --}}
                    @if(!in_array($pengaduan->status, ['resolved', 'rejected']))
                    <div class="mb-5 pb-5 border-b border-gray-100">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Batas Waktu Penanganan</span>
                            @if($pengaduan->is_overdue_sla)
                            <span class="text-orange-600 font-semibold">⚠ Waktu Penanganan Terlampaui</span>
                            @else
                            <span class="text-green-600 font-semibold">✓ Masih Dalam Batas Waktu ({{ $pengaduan->sla_remaining_hours }} jam tersisa)</span>
                            @endif
                        </div>
                        @php
                        $slaHours = $pengaduan->kategori->sla_hours ?? 48;
                        $elapsed = $pengaduan->created_at->diffInHours(now());
                        $pct = min(100, round(($elapsed / $slaHours) * 100));
                        @endphp
                        <div class="mt-2 bg-gray-100 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all {{ $pengaduan->is_overdue_sla ? 'bg-red-400' : 'bg-green-400' }}" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                    @endif

                    {{-- Timeline --}}
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 mb-4">Riwayat Status</h3>
                        <div class="space-y-3">
                            @foreach($pengaduan->timeline as $tl)
                            @php
                            $dotColors = ['pending' => 'bg-yellow-400','verified' => 'bg-blue-400','in_progress' => 'bg-purple-400','resolved' => 'bg-green-400','rejected' => 'bg-red-400'];
                            $labels = ['pending' => 'Menunggu','verified' => 'Terverifikasi','in_progress' => 'Sedang Ditangani','resolved' => 'Selesai','rejected' => 'Ditolak'];
                            @endphp
                            <div class="flex gap-3">
                                <div class="flex flex-col items-center">
                                    <span class="w-3 h-3 rounded-full flex-shrink-0 mt-1 {{ $dotColors[$tl->status] ?? 'bg-gray-300' }}"></span>
                                    @if(!$loop->last)<span class="w-0.5 flex-1 bg-gray-100 mt-1 min-h-3"></span>@endif
                                </div>
                                <div class="pb-3">
                                    <p class="text-sm font-semibold text-gray-800">{{ $labels[$tl->status] ?? $tl->status }}</p>
                                    @if($tl->catatan && !in_array($tl->catatan, ['Auto-assigned berdasarkan kategori', 'Pengaduan dibuat']))<p class="text-xs text-gray-500 mt-0.5">{{ $tl->catatan }}</p>@endif
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $tl->created_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    @if($pengaduan->status === 'resolved' && $pengaduan->solusi)
                    <div class="mt-5 pt-5 border-t border-gray-100">
                        <div class="p-4 bg-green-50 rounded-xl border border-green-100">
                            <p class="text-sm font-bold text-green-800 mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Solusi Penyelesaian
                            </p>
                            <p class="text-sm text-green-700 leading-relaxed">{{ $pengaduan->solusi }}</p>

                            @if($pengaduan->bukti_penyelesaian && count($pengaduan->bukti_penyelesaian) > 0)
                            <div class="mt-3 grid grid-cols-3 sm:grid-cols-4 gap-2">
                                @foreach($pengaduan->bukti_penyelesaian as $foto)
                                <a href="{{ asset('storage/' . $foto) }}" target="_blank" class="block rounded-lg overflow-hidden border border-green-200">
                                    <img src="{{ asset('storage/' . $foto) }}" class="w-full h-16 object-cover" alt="Bukti Selesai">
                                </a>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Features hint --}}
        @if(!isset($pengaduan))
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-2xl w-full">
            @foreach([
                ['icon' => '🔍', 'title' => 'Lacak Real-time', 'desc' => 'Pantau status pengaduan kapan saja'],
                ['icon' => '📋', 'title' => 'Riwayat Lengkap', 'desc' => 'Lihat seluruh timeline pengaduan'],
                ['icon' => '⏱', 'title' => 'Pantau Waktu', 'desc' => 'Ketahui estimasi waktu penanganan'],
            ] as $f)
            <div class="bg-white bg-opacity-70 border border-white rounded-2xl p-4 text-center shadow-sm">
                <p class="text-2xl mb-2">{{ $f['icon'] }}</p>
                <p class="text-sm font-bold text-gray-800">{{ $f['title'] }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $f['desc'] }}</p>
            </div>
            @endforeach
        </div>
        @endif
    </div>

@endsection

