@extends(auth()->check() ? 'layout.dashboard' : 'layout.app')

@section('title', 'Panduan Melapor - SIPS - Sistem Informasi Pengaduan Sekolah')

@section('content')
<div class="py-8 max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="mb-8 text-center animate-in">
        <div class="w-16 h-16 mx-auto bg-gradient-to-br from-pink-500 to-rose-600 text-white rounded-2xl flex items-center justify-center mb-4 shadow-lg shadow-pink-500/30">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Panduan Penggunaan Layanan</h1>
        <p class="text-gray-500 max-w-xl mx-auto">Berikut adalah langkah-langkah, tata cara, dan alur penanganan laporan yang perlu Anda ketahui sebelum mengirimkan pengaduan.</p>
    </div>

    {{-- Cards --}}
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden mb-8">
        <div class="p-8 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-3">
                <span class="w-8 h-8 rounded-lg bg-blue-100 text-white-600 flex items-center justify-center font-bold text-sm">1</span>
                Cara Melapor yang Baik
            </h2>
            <div class="mt-6 space-y-4 text-gray-600 text-sm leading-relaxed">
                <p>Agar laporan Anda dapat ditindaklanjuti dengan cepat oleh tim atau petugas yang bersangkutan, pastikan laporan Anda memenuhi kriteria berikut:</p>
                <ul class="list-disc pl-5 space-y-2">
                    <li><strong>Gunakan Judul yang Spesifik:</strong> Misalnya "Lampu Lab Komputer 2 Mati" alih-alih "Rusak".</li>
                    <li><strong>Deskripsi Kronologis dan Jelas:</strong> Sebutkan apa, siapa, kapan, di mana, dan bagaimana (5W+1H) jika memungkinkan.</li>
                    <li><strong>Pilih Kategori & Urgensi yang Tepat:</strong> Pastikan Anda memilih darurat hanya untuk kejadian yang mengancam nyawa atau kerugian aset sangat besar.</li>
                    <li><strong>Sertakan Bukti Valid:</strong> Lampirkan foto yang menguatkan aduan Anda, pastikan gambar jelas dan tidak rekayasa.</li>
                </ul>
            </div>
        </div>

        <div class="p-8 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-3">
                <span class="w-8 h-8 rounded-lg bg-purple-100 text-white-600 flex items-center justify-center font-bold text-sm">2</span>
                Mengenal Status Laporan
            </h2>
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 rounded-xl bg-yellow-50 border border-yellow-100">
                    <div class="flex items-center gap-2 font-bold text-yellow-700 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Pending (Menunggu Verifikasi)
                    </div>
                    <p class="text-xs text-yellow-800">Laporan baru saja masuk ke sistem kami dan sedang mengantre untuk diverifikasi kelengkapannya oleh admin.</p>
                </div>
                <div class="p-4 rounded-xl bg-blue-50 border border-blue-100">
                    <div class="flex items-center gap-2 font-bold text-blue-700 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Terverifikasi
                    </div>
                    <p class="text-xs text-blue-800">Laporan disetujui, sah, dan telah dialokasikan kepada unit kerja/petugas terkait untuk bersiap ditindaklanjuti.</p>
                </div>
                <div class="p-4 rounded-xl bg-purple-50 border border-purple-100">
                    <div class="flex items-center gap-2 font-bold text-purple-700 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Diproses
                    </div>
                    <p class="text-xs text-purple-800">Petugas kami sedang bekerja di lapangan atau memproses aduan Anda. Harap tunggu hingga penyelesaian.</p>
                </div>
                <div class="p-4 rounded-xl bg-green-50 border border-green-100">
                    <div class="flex items-center gap-2 font-bold text-green-700 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Selesai
                    </div>
                    <p class="text-xs text-green-800">Tindakan perbaikan atau penanganan telah berhasil dieksekusi secara penuh. Laporan ditutup (Resolved).</p>
                </div>
            </div>
        </div>

        <div class="p-8">
            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-3">
                <span class="w-8 h-8 rounded-lg bg-red-100 text-white-600 flex items-center justify-center font-bold text-sm">3</span>
                Aturan & Tata Tertib
            </h2>
            <div class="mt-6 space-y-4 text-gray-600 text-sm leading-relaxed">
                <ul class="space-y-3">
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <p><strong>Larangan Pemalsuan Data:</strong> Segala wujud penyampaian laporan palsu (hoax), fitnah, atau pengaduan iseng dapat dikenai sanksi administratif sekolah hingga pemblokiran akun.</p>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <p><strong>Perlindungan Privasi Pribadi (Laporan Anonim):</strong> Jika Anda enggan identitas Anda diketahui saat melaporkan kasus sensitif (seperti perundungan/bullying), gunakan tombol "Anonim". Identitas Anda akan disembunyikan sepenuhnya dari petugas lapangan, admin, maupun sistem luar.</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="text-center">
        <a href="{{ route('pengaduan.create') }}" class="inline-flex items-center gap-2 px-6 py-3 text-white text-base font-bold rounded-xl transition-all hover:-translate-y-1 hover:shadow-xl shadow-pink-500/20" style="background: linear-gradient(135deg, #cc2c6b, #374151);">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Saya Mengerti, Buat Laporan Sekarang
        </a>
    </div>
</div>
@endsection

