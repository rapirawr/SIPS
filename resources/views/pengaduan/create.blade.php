@extends('layout.dashboard')

@section('title', 'Buat Pengaduan - SIPS - Sistem Informasi Pengaduan Sekolah')

@section('content')
<div class="py-8 max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Buat Pengaduan Baru</h1>
        <p class="text-sm text-gray-500 mt-1">Isi formulir berikut untuk menyampaikan pengaduan</p>
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

    <form action="{{ route('pengaduan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Identity Toggle --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h2 class="text-base font-bold text-gray-900 mb-4">Identitas Pelapor</h2>

            <div class="flex items-center gap-4 mb-5">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="is_anonim" value="0" id="not-anonim" class="w-4 h-4 text-pink-600" onchange="toggleAnonim(false)" {{ old('is_anonim', '0') === '0' ? 'checked' : '' }}>
                    <span class="text-sm font-medium text-gray-700">Dengan Identitas</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="is_anonim" value="1" id="is-anonim" class="w-4 h-4 text-pink-600" onchange="toggleAnonim(true)" {{ old('is_anonim') === '1' ? 'checked' : '' }}>
                    <span class="text-sm font-medium text-gray-700">Anonim</span>
                </label>
            </div>

            <div id="identity-fields" class="{{ old('is_anonim') === '1' ? 'hidden' : '' }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Pelapor</label>
                        <input type="text" name="nama_pelapor" value="{{ old('nama_pelapor', auth()->user()->name ?? '') }}" placeholder="Nama lengkap" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300">
                        @error('nama_pelapor')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Pelapor</label>
                        <input type="email" name="email_pelapor" value="{{ old('email_pelapor', auth()->user()->email ?? '') }}" placeholder="email@example.com" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300">
                        @error('email_pelapor')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Telepon <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <input type="text" name="telp_pelapor" value="{{ old('telp_pelapor') }}" placeholder="08xxxxxxxx" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300">
                    </div>
                </div>
            </div>

            <div id="anonim-notice" class="{{ old('is_anonim') !== '1' ? 'hidden' : '' }} p-3 bg-blue-50 border border-blue-200 rounded-xl">
                <p class="text-sm text-blue-700">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <strong>Mode Anonim:</strong> Identitas Anda tetap tersimpan di sistem namun <strong>hanya Administrator</strong> yang dapat melihatnya. Nama Anda tidak akan muncul di halaman publik, pelacakan, atau di layar petugas lapangan.
                </p>
            </div>
        </div>

        {{-- Pengaduan Details --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h2 class="text-base font-bold text-gray-900 mb-4">Detail Pengaduan</h2>

            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                        <select name="kategori_id" required class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300 bg-white">
                            <option value="">Pilih kategori...</option>
                            @foreach($kategoris as $k)
                            <option value="{{ $k->id }}" {{ old('kategori_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                            @endforeach
                        </select>
                        @error('kategori_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tingkat Urgensi <span class="text-red-500">*</span></label>
                        <select name="tingkat_urgensi" required class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300 bg-white">
                            <option value="">Pilih urgensi...</option>
                            <option value="rendah" {{ old('tingkat_urgensi') == 'rendah' ? 'selected' : '' }}>🟢 Rendah</option>
                            <option value="sedang" {{ old('tingkat_urgensi') == 'sedang' ? 'selected' : '' }}>🔵 Sedang</option>
                            <option value="tinggi" {{ old('tingkat_urgensi') == 'tinggi' ? 'selected' : '' }}>🟠 Tinggi</option>
                            <option value="darurat" {{ old('tingkat_urgensi') == 'darurat' ? 'selected' : '' }}>🔴 Darurat</option>
                        </select>
                        @error('tingkat_urgensi')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul Pengaduan <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" value="{{ old('judul') }}" required placeholder="Deskripsikan masalah secara singkat..." class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300">
                    @error('judul')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi Lengkap <span class="text-red-500">*</span></label>
                    <textarea name="deskripsi" required rows="5" placeholder="Jelaskan pengaduan Anda secara lengkap dan detail..." class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300 resize-none">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Lokasi Kejadian <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <input type="text" name="lokasi_kejadian" value="{{ old('lokasi_kejadian') }}" placeholder="Misal: Lab Komputer 2, Lantai 3" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Kejadian <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <input type="date" name="tanggal_kejadian" value="{{ old('tanggal_kejadian') }}" max="{{ date('Y-m-d') }}" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300">
                    </div>
                </div>
            </div>
        </div>

        {{-- Bukti Foto --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h2 class="text-base font-bold text-gray-900 mb-4">Bukti Foto <span class="text-gray-400 font-normal text-sm">(opsional)</span></h2>

            <label for="bukti_foto" class="block cursor-pointer">
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center hover:border-pink-300 hover:bg-pink-50 transition-colors" id="dropzone">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <p class="text-sm text-gray-500">Klik untuk upload atau drag & drop</p>
                    <p class="text-xs text-gray-400 mt-1">PNG, JPG, JPEG (max 5MB per file)</p>
                </div>
                <input type="file" id="bukti_foto" name="bukti_foto[]" multiple accept="image/*" class="hidden" onchange="previewFiles(this)">
            </label>
            <div id="preview-container" class="mt-4 flex flex-wrap gap-3"></div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('dashboard') }}" class="px-6 py-2.5 text-sm font-semibold text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">Batal</a>
            <button type="submit" class="px-6 py-2.5 text-sm text-white font-bold rounded-xl transition-all hover:-translate-y-0.5 hover:shadow-lg" style="background: linear-gradient(135deg, #cc2c6b, #374151);">
                Kirim Pengaduan
            </button>
        </div>
    </form>
</div>

<script>
function toggleAnonim(isAnonim) {
    const fields = document.getElementById('identity-fields');
    const notice = document.getElementById('anonim-notice');
    fields.classList.toggle('hidden', isAnonim);
    notice.classList.toggle('hidden', !isAnonim);
}

function previewFiles(input) {
    const container = document.getElementById('preview-container');
    container.innerHTML = '';
    if (input.files && input.files.length > 0) {
        Array.from(input.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative group';
                div.innerHTML = `<img src="${e.target.result}" class="w-20 h-20 object-cover rounded-xl border border-gray-200">
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 rounded-xl transition-all"></div>`;
                container.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }
}
</script>
@endsection

