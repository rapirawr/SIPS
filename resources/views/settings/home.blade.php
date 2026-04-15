@extends('layout.dashboard')

@section('title', 'Pengaturan Beranda - SIPS - Sarana Informasi Pengaduan Sekolah')

@section('content')
<div class="py-8 max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Pengaturan Teks Beranda</h1>
        <p class="text-sm text-gray-500 mt-1">Ubah kata-kata yang tampil di halaman depan website sesuai kebutuhan sekolah Anda.</p>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('settings.home.update') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Section Hero --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Bagian 1: Hero Banner Atas</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Pil Label Atas (Badge)</label>
                    <input type="text" name="hero_pill" value="{{ old('hero_pill', $settings['hero_pill'] ?? '') }}" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200" required>
                    <p class="text-xs text-gray-400 mt-1">Contoh: "Platform Pengaduan Digital SMKN 1 Bondowoso"</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Teks Utama 1</label>
                        <input type="text" name="hero_title_1" value="{{ old('hero_title_1', $settings['hero_title_1'] ?? '') }}" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-pink-600 mb-1">Teks Utama Berwarna</label>
                        <input type="text" name="hero_title_gradient" value="{{ old('hero_title_gradient', $settings['hero_title_gradient'] ?? '') }}" class="w-full border-pink-300 rounded-xl shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Teks Utama 2</label>
                        <input type="text" name="hero_title_2" value="{{ old('hero_title_2', $settings['hero_title_2'] ?? '') }}" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi Panjang (Sub-judul)</label>
                    <textarea name="hero_description" rows="3" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200" required>{{ old('hero_description', $settings['hero_description'] ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Section Features --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Bagian 2: Kenapa Harus Melapor?</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Judul Seksi Fitur</label>
                    <input type="text" name="feature_title" value="{{ old('feature_title', $settings['feature_title'] ?? '') }}" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200" required>
                    <p class="text-xs text-gray-400 mt-1">Gunakan kode HTML jika perlu warna khusus, contoh: <code>Mengapa Harus &lt;span class="text-pink-600"&gt;Melaporkan?&lt;/span&gt;</code></p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Sub-judul Fitur</label>
                    <textarea name="feature_description" rows="2" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200" required>{{ old('feature_description', $settings['feature_description'] ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('dashboard') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors font-medium">Batal</a>
            <button type="submit" class="px-6 py-2 text-white font-bold rounded-xl transition-all shadow-md hover:shadow-lg" style="background: linear-gradient(135deg, #cc2c6b, #374151);">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection

