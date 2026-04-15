@extends('layout.dashboard')

@section('title', 'Detail Pengaduan - ' . $pengaduan->kode_unik)

@section('content')
<div class="py-8">
    {{-- Back + Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="/pengaduan" class="p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Detail Pengaduan</h1>
            <span class="font-mono text-sm text-gray-500">{{ $pengaduan->kode_unik }}</span>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Status Card --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1.5 rounded-xl text-sm font-bold {{ $pengaduan->status_badge['class'] }}">
                            {{ $pengaduan->status_badge['text'] }}
                        </span>
                        <span class="px-3 py-1.5 rounded-xl text-sm font-semibold {{ $pengaduan->urgensi_badge['class'] }}">
                            {{ $pengaduan->urgensi_badge['text'] }}
                        </span>
                        @if($pengaduan->is_overdue_sla)
                        <span class="px-3 py-1.5 rounded-xl text-sm font-semibold bg-orange-100 text-orange-600">
                            ⚠ Waktu Penanganan Terlampaui
                        </span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        @can('update', $pengaduan)
                        <a href="{{ route('pengaduan.edit', $pengaduan) }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-blue-600 border border-blue-200 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </a>
                        @endcan
                        @can('delete', $pengaduan)
                        <form action="{{ route('pengaduan.destroy', $pengaduan) }}" method="POST" onsubmit="return confirm('Yakin hapus pengaduan ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-red-600 border border-red-200 bg-red-50 rounded-xl hover:bg-red-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Hapus
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>

                <h2 class="text-xl font-bold text-gray-900 mb-3">{{ $pengaduan->judul }}</h2>
                <p class="text-gray-600 leading-relaxed text-sm">{{ $pengaduan->deskripsi }}</p>
            </div>

            {{-- Pengaduan Info --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                <h3 class="text-base font-bold text-gray-900 mb-4">Informasi Pengaduan</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-400 font-medium mb-1">Kategori</p>
                        <p class="text-gray-800 font-semibold">{{ $pengaduan->kategori->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 font-medium mb-1">Pelapor</p>
                        <p class="text-gray-800 font-semibold">
                            @if($pengaduan->is_anonim)
                                @if(auth()->user()->isAdmin())
                                    {{ $pengaduan->nama_pelapor ?? ($pengaduan->user->name ?? '-') }}
                                    <span class="ml-2 px-1.5 py-0.5 rounded bg-gray-100 text-[10px] text-gray-500 font-bold uppercase tracking-wider">Anonim</span>
                                @else
                                    <span class="text-gray-400 italic">Identitas Tersembunyi (Anonim)</span>
                                @endif
                            @else
                                {{ $pengaduan->nama_pelapor ?? ($pengaduan->user->name ?? '-') }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-400 font-medium mb-1">Tanggal Lapor</p>
                        <p class="text-gray-800 font-semibold">{{ $pengaduan->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    @if($pengaduan->tanggal_kejadian)
                    <div>
                        <p class="text-gray-400 font-medium mb-1">Tanggal Kejadian</p>
                        <p class="text-gray-800 font-semibold">{{ $pengaduan->tanggal_kejadian->format('d M Y') }}</p>
                    </div>
                    @endif
                    @if($pengaduan->lokasi_kejadian)
                    <div>
                        <p class="text-gray-400 font-medium mb-1">Lokasi</p>
                        <p class="text-gray-800 font-semibold">{{ $pengaduan->lokasi_kejadian }}</p>
                    </div>
                    @endif
                    @if($pengaduan->assignedUser)
                    <div>
                        <p class="text-gray-400 font-medium mb-1">Ditangani oleh</p>
                        <p class="text-gray-800 font-semibold">{{ $pengaduan->assignedUser->name }}</p>
                    </div>
                    @endif
                    @if($pengaduan->resolved_at)
                    <div>
                        <p class="text-gray-400 font-medium mb-1">Diselesaikan</p>
                        <p class="text-gray-800 font-semibold">{{ $pengaduan->resolved_at->format('d M Y, H:i') }}</p>
                    </div>
                    @endif
                    @if($pengaduan->durasi_penanganan)
                    <div>
                        <p class="text-gray-400 font-medium mb-1">Durasi Penanganan</p>
                        <p class="text-gray-800 font-semibold">{{ $pengaduan->durasi_penanganan_formatted }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Bukti Foto --}}
            @if($pengaduan->bukti_foto && count($pengaduan->bukti_foto) > 0)
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                <h3 class="text-base font-bold text-gray-900 mb-4">Bukti Foto</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($pengaduan->bukti_foto as $foto)
                    <a href="{{ asset('storage/' . $foto) }}" target="_blank" class="group block overflow-hidden rounded-xl border border-gray-200 hover:shadow-md transition-shadow">
                        <img src="{{ asset('storage/' . $foto) }}" class="w-full h-36 object-cover group-hover:scale-105 transition-transform duration-300" alt="Bukti">
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Solusi / Bukti Penyelesaian --}}
            @if($pengaduan->solusi)
            <div class="bg-green-50 border border-green-200 rounded-2xl p-6">
                <h3 class="text-base font-bold text-green-800 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Solusi / Penyelesaian
                </h3>
                <p class="text-sm text-green-700 leading-relaxed">{{ $pengaduan->solusi }}</p>

                @if($pengaduan->bukti_penyelesaian && count($pengaduan->bukti_penyelesaian) > 0)
                <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($pengaduan->bukti_penyelesaian as $foto)
                    <a href="{{ asset('storage/' . $foto) }}" target="_blank" class="block overflow-hidden rounded-xl border border-green-300 hover:shadow-md transition-shadow">
                        <img src="{{ asset('storage/' . $foto) }}" class="w-full h-32 object-cover" alt="Bukti Penyelesaian">
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
            @endif

            {{-- Quick Update Status (Admin/Petugas) --}}
            @if(auth()->check() && auth()->user()->isPetugas())
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm" x-data="{ status: '{{ $pengaduan->status }}' }">
                <h3 class="text-base font-bold text-gray-900 mb-4">Update Status Pengaduan</h3>
                <form action="{{ route('pengaduan.update-status', $pengaduan) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase mb-1 block">Status</label>
                                <select name="status" x-model="status" required class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-300 bg-white">
                                    <option value="pending">Pending</option>
                                    <option value="verified">Terverifikasi</option>
                                    <option value="in_progress">Diproses</option>
                                    <option value="resolved">Selesai</option>
                                    <option value="rejected">Ditolak</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase mb-1 block">Catatan Tambahan (Opsional)</label>
                                <input type="text" name="catatan" placeholder="Catatan untuk riwayat status" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-300">
                            </div>
                        </div>

                        {{-- Final Fields when resolved --}}
                        <div x-show="status == 'resolved'" x-transition class="space-y-4 pt-4 border-t border-dashed border-gray-100">
                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase mb-1 block">Solusi / Langkah Perbaikan</label>
                                <textarea name="solusi" rows="3" placeholder="Jelaskan bagaimana masalah ini diselesaikan..." class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-300"></textarea>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase mb-1 block">Bukti Penyelesaian (Foto)</label>
                                <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl p-4 text-center">
                                    <input type="file" name="bukti_penyelesaian[]" multiple accept="image/*" class="hidden" id="notif-evidence-input">
                                    <label for="notif-evidence-input" class="cursor-pointer">
                                        <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <p class="text-xs text-gray-400">Pilih atau Seret foto bukti pengerjaan ke sini</p>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full sm:w-auto px-8 py-3 text-white text-sm font-bold rounded-xl transition-all hover:shadow-lg shadow-pink-500/20" style="background: linear-gradient(135deg, #cc2c6b, #374151);">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Timeline --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                <h3 class="text-base font-bold text-gray-900 mb-5">Riwayat Status</h3>
                <div class="space-y-4">
                    @forelse($pengaduan->timeline as $tl)
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            @php
                            $dotColors = [
                                'pending' => 'bg-yellow-400',
                                'verified' => 'bg-blue-400',
                                'in_progress' => 'bg-purple-400',
                                'resolved' => 'bg-green-400',
                                'rejected' => 'bg-red-400',
                            ];
                            @endphp
                            <span class="w-3 h-3 rounded-full flex-shrink-0 mt-0.5 {{ $dotColors[$tl->status] ?? 'bg-gray-300' }}"></span>
                            @if(!$loop->last)
                            <span class="w-0.5 flex-1 bg-gray-100 mt-1 min-h-4"></span>
                            @endif
                        </div>
                        <div class="pb-3">
                            <p class="text-sm font-semibold text-gray-800">
                                @php
                                $labels = ['pending' => 'Pending', 'verified' => 'Terverifikasi', 'in_progress' => 'Diproses', 'resolved' => 'Selesai', 'rejected' => 'Ditolak'];
                                @endphp
                                {{ $labels[$tl->status] ?? $tl->status }}
                            </p>
                            @if($tl->catatan)
                            <p class="text-xs text-gray-500 mt-0.5">{{ $tl->catatan }}</p>
                            @endif
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $tl->created_at->format('d M Y, H:i') }}
                                @if($tl->updatedBy)· {{ $tl->updatedBy->name }}@endif
                            </p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400">Belum ada riwayat status</p>
                    @endforelse
                </div>
            </div>

            {{-- Assign Petugas (Admin) --}}
            @if(auth()->check() && auth()->user()->isAdmin())
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                <h3 class="text-base font-bold text-gray-900 mb-4">Tugaskan ke Petugas</h3>
                <form action="{{ route('pengaduan.assign', $pengaduan) }}" method="POST">
                    @csrf
                    <div class="space-y-3">
                        <select name="assigned_to" class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-300 bg-white">
                            <option value="">Pilih petugas...</option>
                            @foreach(\App\Models\User::petugas()->active()->get() as $petugas)
                            <option value="{{ $petugas->id }}" {{ $pengaduan->assigned_to == $petugas->id ? 'selected' : '' }}>
                                {{ $petugas->name }} ({{ $petugas->role }})
                            </option>
                            @endforeach
                        </select>
                        <input type="text" name="catatan" placeholder="Catatan penugasan (opsional)" class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-300">
                        <button type="submit" class="w-full py-2.5 text-white text-sm font-semibold rounded-xl transition-all" style="background: linear-gradient(135deg, #cc2c6b, #374151);">
                            Tugaskan
                        </button>
                    </div>
                </form>
            </div>
            @endif

            {{-- Batas Waktu Penanganan --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                <h3 class="text-base font-bold text-gray-900 mb-4">Batas Waktu Penanganan</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Target Selesai</span>
                        <span class="font-semibold text-gray-700">{{ $pengaduan->kategori->sla_hours ?? 48 }} jam</span>
                    </div>
                    @if(!in_array($pengaduan->status, ['resolved', 'rejected']))
                    <div class="flex justify-between">
                        <span class="text-gray-500">Sisa Waktu</span>
                        <span class="font-semibold {{ $pengaduan->is_overdue_sla ? 'text-red-600' : 'text-green-600' }}">
                            {{ $pengaduan->sla_remaining_formatted }}
                        </span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-500">Dibuat</span>
                        <span class="font-semibold text-gray-700">{{ $pengaduan->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

