@extends('layout.dashboard')

@section('title', 'Tambah Kategori - SIPS - Sistem Informasi Pengaduan Sekolah')

@section('content')
<div class="py-8 max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('kategori.index') }}" class="p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Tambah Kategori</h1>
            <p class="text-sm text-gray-500 mt-0.5">Buat kategori pengaduan baru</p>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
        <p class="text-sm font-semibold text-red-700 mb-2">Terdapat kesalahan:</p>
        <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('kategori.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h2 class="text-base font-bold text-gray-900 mb-5">Informasi Kategori</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Kategori <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Misal: Sarana Prasarana, Kebersihan..." class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300">
                    @error('nama')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" placeholder="Deskripsi singkat kategori..." class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300 resize-none">{{ old('deskripsi') }}</textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Icon (Emoji)</label>
                        <input type="text" name="icon" value="{{ old('icon') }}" placeholder="📋" maxlength="10" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300 text-center text-xl">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Warna <span class="text-red-500">*</span></label>
                        <div class="flex gap-2">
                            <input type="color" name="warna" id="warna-picker" value="{{ old('warna', '#cc2c6b') }}" class="w-12 h-10 rounded-xl border border-gray-200 cursor-pointer p-0.5" oninput="updateColorPreview(this.value)">
                            <input type="text" id="warna-text" value="{{ old('warna', '#cc2c6b') }}" placeholder="#cc2c6b" class="flex-1 px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300" oninput="document.getElementById('warna-picker').value = this.value; updateColorPreview(this.value)">
                        </div>
                        @error('warna')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        <script>
                        function updateColorPreview(v) {
                            document.getElementById('warna-picker').value = v;
                            document.getElementById('warna-text').value = v;
                            document.querySelector('input[name="warna"]').value = v;
                        }
                        </script>
                        {{-- Hidden real input --}}
                        <input type="hidden" name="warna" id="warna-real" value="{{ old('warna', '#cc2c6b') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Urutan</label>
                        <input type="number" name="urutan" value="{{ old('urutan', 1) }}" min="1" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300">
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h2 class="text-base font-bold text-gray-900 mb-5">Batas Waktu & PIC</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Batas Waktu (jam) <span class="text-red-500">*</span></label>
                    <input type="number" name="sla_hours" value="{{ old('sla_hours', 48) }}" required min="1" max="720" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300">
                    <p class="text-xs text-gray-400 mt-1">Waktu maksimal penanganan dalam jam (1-720)</p>
                    @error('sla_hours')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">PIC Default</label>
                    <select name="pic_default_id" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300 bg-white">
                        <option value="">Tidak ada PIC default</option>
                        @foreach($petugas as $p)
                        <option value="{{ $p->id }}" {{ old('pic_default_id') == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->role }})</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Pengaduan akan otomatis ditugaskan ke PIC ini</p>
                </div>
                <div class="flex items-center gap-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="w-4 h-4 rounded text-pink-600 border-gray-300 focus:ring-pink-300">
                    <label for="is_active" class="text-sm font-medium text-gray-700 cursor-pointer">Kategori aktif (dapat digunakan pelapor)</label>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('kategori.index') }}" class="px-6 py-2.5 text-sm font-semibold text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">Batal</a>
            <button type="submit" class="px-6 py-2.5 text-sm text-white font-bold rounded-xl transition-all hover:-translate-y-0.5 hover:shadow-lg" style="background: linear-gradient(135deg, #cc2c6b, #374151);">
                Simpan Kategori
            </button>
        </div>
    </form>
</div>

<script>
    // Sync color inputs properly
    document.querySelector('input[type="color"]').addEventListener('input', function() {
        document.getElementById('warna-text').value = this.value;
        document.getElementById('warna-real').value = this.value;
    });
    document.getElementById('warna-text').addEventListener('input', function() {
        document.getElementById('warna-picker').value = this.value;
        document.getElementById('warna-real').value = this.value;
    });
    // Remove extra hidden input issue
    document.querySelector('form').addEventListener('submit', function() {
        const picker = document.querySelector('input[type="color"]');
        document.getElementById('warna-real').value = picker.value;
        // Disable the other warna inputs
        document.querySelectorAll('input[name="warna"]:not(#warna-real)').forEach(i => i.disabled = true);
    });
</script>
@endsection

