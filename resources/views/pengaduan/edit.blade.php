@extends('layout.dashboard')

@section('title', 'Edit Pengaduan - ' . $pengaduan->kode_unik)

@section('content')
<div class="py-8 max-w-3xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('pengaduan.show', $pengaduan->kode_unik) }}" class="p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Edit Pengaduan</h1>
            <span class="font-mono text-sm text-gray-500">{{ $pengaduan->kode_unik }}</span>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
        <p class="text-sm font-semibold text-red-700 mb-2">Terdapat kesalahan:</p>
        <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('pengaduan.update', $pengaduan) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')

        {{-- Detail Pengaduan --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h2 class="text-base font-bold text-gray-900 mb-4">Detail Pengaduan</h2>
            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                        <select name="kategori_id" required class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300 bg-white">
                            @foreach($kategoris as $k)
                            <option value="{{ $k->id }}" {{ old('kategori_id', $pengaduan->kategori_id) == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                            @endforeach
                        </select>
                        @error('kategori_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tingkat Urgensi <span class="text-red-500">*</span></label>
                        <select name="tingkat_urgensi" required class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300 bg-white">
                            <option value="rendah" {{ old('tingkat_urgensi', $pengaduan->tingkat_urgensi) == 'rendah' ? 'selected' : '' }}>🟢 Rendah</option>
                            <option value="sedang" {{ old('tingkat_urgensi', $pengaduan->tingkat_urgensi) == 'sedang' ? 'selected' : '' }}>🔵 Sedang</option>
                            <option value="tinggi" {{ old('tingkat_urgensi', $pengaduan->tingkat_urgensi) == 'tinggi' ? 'selected' : '' }}>🟠 Tinggi</option>
                            <option value="darurat" {{ old('tingkat_urgensi', $pengaduan->tingkat_urgensi) == 'darurat' ? 'selected' : '' }}>🔴 Darurat</option>
                        </select>
                        @error('tingkat_urgensi')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul Pengaduan <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" value="{{ old('judul', $pengaduan->judul) }}" required class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300">
                    @error('judul')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi <span class="text-red-500">*</span></label>
                    <textarea name="deskripsi" rows="5" required class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300 resize-none">{{ old('deskripsi', $pengaduan->deskripsi) }}</textarea>
                    @error('deskripsi')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Lokasi Kejadian</label>
                        <input type="text" name="lokasi_kejadian" value="{{ old('lokasi_kejadian', $pengaduan->lokasi_kejadian) }}" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Kejadian</label>
                        <input type="date" name="tanggal_kejadian" value="{{ old('tanggal_kejadian', $pengaduan->tanggal_kejadian?->format('Y-m-d')) }}" max="{{ date('Y-m-d') }}" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300">
                    </div>
                </div>
            </div>
        </div>

        {{-- Admin/Petugas Fields --}}
        @if(auth()->user()->isPetugas())
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h2 class="text-base font-bold text-gray-900 mb-4">Penanganan (Petugas)</h2>
            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Status <span class="text-red-500">*</span></label>
                        <select name="status" required class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300 bg-white">
                            <option value="pending" {{ old('status', $pengaduan->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="verified" {{ old('status', $pengaduan->status) == 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                            <option value="in_progress" {{ old('status', $pengaduan->status) == 'in_progress' ? 'selected' : '' }}>Diproses</option>
                            <option value="resolved" {{ old('status', $pengaduan->status) == 'resolved' ? 'selected' : '' }}>Selesai</option>
                            <option value="rejected" {{ old('status', $pengaduan->status) == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                        @error('status')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    @if(auth()->user()->isAdmin())
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tugaskan ke Petugas</label>
                        <select name="assigned_to" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300 bg-white">
                            <option value="">Pilih petugas...</option>
                            @foreach($petugas as $p)
                            <option value="{{ $p->id }}" {{ old('assigned_to', $pengaduan->assigned_to) == $p->id ? 'selected' : '' }}>
                                {{ $p->name }} ({{ $p->role }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan Perubahan Status</label>
                    <input type="text" name="catatan_perubahan" value="{{ old('catatan_perubahan') }}" placeholder="Catatan untuk riwayat status..." class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan Internal</label>
                    <textarea name="catatan_internal" rows="3" placeholder="Catatan internal (tidak terlihat pelapor)..." class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300 resize-none">{{ old('catatan_internal', $pengaduan->catatan_internal) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Solusi / Penyelesaian</label>
                    <textarea name="solusi" rows="4" placeholder="Jelaskan solusi yang diberikan..." class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300 resize-none">{{ old('solusi', $pengaduan->solusi) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Bukti Penyelesaian</label>
                    <input type="file" name="bukti_penyelesaian[]" multiple accept="image/*" class="w-full text-sm text-gray-600 border border-gray-200 rounded-xl px-4 py-2.5 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-pink-50 file:text-pink-600 hover:file:bg-pink-100">
                    @if($pengaduan->bukti_penyelesaian && count($pengaduan->bukti_penyelesaian) > 0)
                    <div class="mt-3 flex gap-2 flex-wrap">
                        @foreach($pengaduan->bukti_penyelesaian as $foto)
                        <img src="{{ asset('storage/' . $foto) }}" class="w-16 h-16 object-cover rounded-lg border border-gray-200" alt="Bukti">
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @else
        {{-- Non-petugas must still submit status if required --}}
        <input type="hidden" name="status" value="{{ $pengaduan->status }}">
        @endif

        {{-- Submit --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('pengaduan.show', $pengaduan->kode_unik) }}" class="px-6 py-2.5 text-sm font-semibold text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">Batal</a>
            <button type="submit" class="px-6 py-2.5 text-sm text-white font-bold rounded-xl transition-all hover:-translate-y-0.5 hover:shadow-lg" style="background: linear-gradient(135deg, #cc2c6b, #374151);">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection

