@extends('layout.app')

@section('title', 'SIPS - Sarana Informasi Pengaduan Sekolah')

@section('content')

{{-- Load floating logo assets if any, currently handled by inline CSS --}}

<!-- ═══════════════════════════════════════════════════════════
     HERO SECTION — 3D Three.js + Split Layout
═══════════════════════════════════════════════════════════ -->
<section id="hero-section" class="hero-section relative min-h-screen flex items-center overflow-hidden">

    <!-- Gradient mesh background -->
    <div class="hero-bg-mesh"></div>

    <!-- Grain texture overlay -->
    <div class="hero-grain"></div>

    <div class="max-w-7xl mx-auto px-6 pt-28 pb-16 md:pt-32 md:pb-24 relative z-10 w-full">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">

            <!-- LEFT: Copy -->
            <div class="space-y-7 hero-left reveal">
                <!-- Badge pill -->
                <div class="inline-flex items-center gap-2.5 px-5 py-2.5 hero-pill rounded-full text-sm font-bold">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="absolute inline-flex h-full w-full rounded-full bg-pink-400 opacity-75 animate-ping"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5" style="background:#cc2c6b"></span>
                    </span>
                    <span class="hero-pill-text">{{ $settings['hero_pill'] ?? 'Platform Pengaduan Digital SMKN 1 Bondowoso' }}</span>
                </div>

                <!-- Heading -->
                <h1 class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-black leading-[1.05] tracking-tight text-white">
                    {{ $settings['hero_title_1'] ?? 'Suarakan' }}
                    <span class="hero-gradient-text block">{{ $settings['hero_title_gradient'] ?? 'Aspirasimu,' }}</span>
                    <span class="block">{{ $settings['hero_title_2'] ?? 'Wujudkan Perubahan!' }}</span>
                </h1>

                <!-- Description -->
                <p class="text-base sm:text-lg text-white/60 leading-relaxed max-w-xl">
                    {{ $settings['hero_description'] ?? 'Platform digital transparan untuk menyampaikan aspirasi dan melaporkan masalah di sekolah. Setiap suara didengar, setiap laporan ditindaklanjuti.' }}
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('pengaduan.create') }}" id="btn-hero-cta"
                       class="hero-btn-primary group inline-flex items-center gap-3 px-7 py-4 text-white rounded-2xl font-bold text-base sm:text-lg">
                        <svg class="w-5 h-5 transition-transform group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Buat Laporan
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                    <a href="{{ route('panduan') }}" id="btn-hero-guide"
                       class="hero-btn-secondary inline-flex items-center gap-2 px-7 py-4 rounded-2xl font-bold text-base sm:text-lg">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        Panduan
                    </a>
                </div>

                <!-- Live stats micro-pills -->
                <div class="flex flex-wrap gap-3 pt-2">
                    <div class="hero-stat-chip hero-stat-pink">
                        <span class="hero-stat-num">{{ number_format($total_laporan) }}</span>
                        <span class="hero-stat-lbl">Laporan</span>
                    </div>
                    <div class="hero-stat-chip hero-stat-indigo">
                        <span class="hero-stat-num">{{ $resolution_rate }}%</span>
                        <span class="hero-stat-lbl">Selesai</span>
                    </div>
                    <div class="hero-stat-chip hero-stat-emerald">
                        <span class="hero-stat-num">24/7</span>
                        <span class="hero-stat-lbl">Aktif</span>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Three.js Canvas -->
            <div class="relative flex justify-center items-center hero-right reveal reveal-delay-200">
                <!-- Glow rings behind canvas -->
                <div class="hero-glow-ring hero-glow-1"></div>
                <div class="hero-glow-ring hero-glow-2"></div>

                <!-- Floating Logo Container -->
                <div class="relative flex justify-center items-center w-full h-full min-h-[400px] md:min-h-[500px]">
                    <!-- Tight wrapper — everything inside is centered on the logo -->
                    <div class="hero-logo-wrapper">
                        
                        <!-- Spotlight Beam (trapezoid light cone from below) -->
                        <div class="hero-spotlight-beam"></div>

                        <!-- Spotlight Base (glowing ellipse at the bottom) -->
                        <div class="hero-spotlight"></div>
                        
                        <!-- Floating Logo Image -->
                        <img src="{{ asset('storage/asset/img/logo.webp') }}" alt="SIPS Logo" class="hero-floating-logo">
                    </div>
                </div>

                <!-- Floating info cards -->
                <div class="hero-float-card hero-float-card-top">
                    <div class="hero-float-icon bg-green-500/10">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <div class="font-bold text-white text-sm">Terverifikasi</div>
                        <div class="text-xs text-white/50">Proses otomatis</div>
                    </div>
                </div>

                <div class="hero-float-card hero-float-card-bottom">
                    <div class="hero-float-icon bg-blue-500/10">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="font-bold text-white text-sm">Respon Cepat</div>
                        <div class="text-xs text-white/50">Dalam 2×24 jam</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll indicator -->
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-10 hidden md:flex flex-col items-center gap-2 animate-bounce">
        <span class="text-xs font-medium text-white/40 tracking-widest uppercase">Scroll</span>
        <svg class="w-5 h-5 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7"/></svg>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     STATS BANNER
═══════════════════════════════════════════════════════════ -->
<!-- <div class="stats-banner py-10 reveal" id="stats-banner">
    <div class="max-w-5xl mx-auto px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="text-center text-white">
                <div class="text-3xl sm:text-4xl font-black mb-1 counter" data-target="{{ $processed_laporan }}">0</div>
                <div class="text-white/70 text-xs sm:text-sm font-medium">Laporan Diproses</div>
            </div>
            <div class="text-center text-white">
                <div class="text-3xl sm:text-4xl font-black mb-1"><span class="counter" data-target="{{ $response_rate }}">0</span>%</div>
                <div class="text-white/70 text-xs sm:text-sm font-medium">Response Rate</div>
            </div>
            <div class="text-center text-white">
                <div class="text-3xl sm:text-4xl font-black mb-1">4.9<span class="text-2xl text-white/50">/5</span></div>
                <div class="text-white/70 text-xs sm:text-sm font-medium">Rating Kepuasan</div>
            </div>
            <div class="text-center text-white">
                <div class="text-3xl sm:text-4xl font-black mb-1 counter" data-target="{{ $kategori_count }}">0</div>
                <div class="text-white/70 text-xs sm:text-sm font-medium">Kategori Laporan</div>
            </div>
        </div>
    </div>
</div> -->

<!-- ═══════════════════════════════════════════════════════════
     FEATURES SECTION
═══════════════════════════════════════════════════════════ -->
<section id="fitur" class="py-20 md:py-28 bg-gray-50/80">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-14 reveal">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-pink-50 rounded-full text-sm font-bold text-pink-700 mb-4 border border-pink-100">
                <svg class="w-4 h-4 text-pink-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                Fitur Unggulan
            </div>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 mb-4">
                {{ $settings['feature_title'] ?? 'Mengapa Harus Melaporkan?' }}
            </h2>
            <p class="text-gray-500 max-w-2xl mx-auto text-base sm:text-lg">
                {{ $settings['feature_description'] ?? 'Dirancang untuk memberikan pengalaman pelaporan yang mudah, aman, dan efektif' }}
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            <!-- Feature cards -->
            <div class="feat-card reveal" data-accent="#cc2c6b">
                <div class="feat-icon-wrap" style="background:rgba(204,44,107,0.08)">
                    <svg class="w-7 h-7" style="color:#cc2c6b" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Aman & Terpercaya</h3>
                <p class="text-gray-500 leading-relaxed text-sm">Identitas Anda dilindungi dengan enkripsi tingkat enterprise. Privasi adalah prioritas kami.</p>
            </div>
            <div class="feat-card reveal reveal-delay-200" data-accent="#7c3aed">
                <div class="feat-icon-wrap" style="background:rgba(124,58,237,0.08)">
                    <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Respon Cepat</h3>
                <p class="text-gray-500 leading-relaxed text-sm">Tim kami memproses setiap laporan dalam 24 jam dengan tindak lanjut yang jelas.</p>
            </div>
            <div class="feat-card reveal reveal-delay-400" data-accent="#0891b2">
                <div class="feat-icon-wrap" style="background:rgba(8,145,178,0.08)">
                    <svg class="w-7 h-7 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Mudah & Intuitif</h3>
                <p class="text-gray-500 leading-relaxed text-sm">Interface yang ramah pengguna — laporkan masalah hanya dalam beberapa klik.</p>
            </div>
            <div class="feat-card reveal" data-accent="#2563eb">
                <div class="feat-icon-wrap" style="background:rgba(37,99,235,0.08)">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Notifikasi Real-time</h3>
                <p class="text-gray-500 leading-relaxed text-sm">Update status laporan Anda langsung melalui notifikasi dan email otomatis.</p>
            </div>
            <div class="feat-card reveal reveal-delay-200" data-accent="#db2777">
                <div class="feat-icon-wrap" style="background:rgba(219,39,119,0.08)">
                    <svg class="w-7 h-7 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Tracking Transparan</h3>
                <p class="text-gray-500 leading-relaxed text-sm">Pantau progres penyelesaian laporan Anda dengan sarana tracking yang jelas.</p>
            </div>
            <div class="feat-card reveal reveal-delay-400" data-accent="#16a34a">
                <div class="feat-icon-wrap" style="background:rgba(22,163,74,0.08)">
                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Dukungan Komunitas</h3>
                <p class="text-gray-500 leading-relaxed text-sm">Bergabung dengan komunitas untuk menciptakan perubahan positif bersama.</p>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     HOW IT WORKS — Steps
═══════════════════════════════════════════════════════════ -->
<section class="py-20 md:py-28 how-section relative overflow-hidden" id="cara-kerja">
    <div class="absolute top-0 right-0 w-96 h-96 bg-pink-500/5 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-72 h-72 bg-indigo-500/5 rounded-full translate-y-1/2 -translate-x-1/2 blur-3xl"></div>

    <div class="max-w-7xl mx-auto px-6 relative z-10">
        <div class="text-center mb-14 reveal">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur rounded-full text-sm font-bold text-white mb-4 border border-white/20">
                <svg class="w-4 h-4 text-yellow-300 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Cara Kerja
            </div>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white mb-4">
                Laporkan dalam <span class="text-yellow-300">4 Langkah</span> Mudah
            </h2>
            <p class="text-white/50 max-w-2xl mx-auto text-base sm:text-lg">Proses yang sederhana dan transparan dari awal hingga selesai</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="step-card reveal">
                <div class="step-num" style="background:linear-gradient(135deg,#cc2c6b,#ec4899)">1</div>
                <div class="step-icon-box"><svg class="w-10 h-10 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">Buat Laporan</h3>
                <p class="text-gray-500 text-sm">Isi formulir dengan detail masalah yang ingin dilaporkan</p>
            </div>
            <div class="step-card reveal reveal-delay-200">
                <div class="step-num" style="background:linear-gradient(135deg,#7c3aed,#a78bfa)">2</div>
                <div class="step-icon-box"><svg class="w-10 h-10 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">Verifikasi</h3>
                <p class="text-gray-500 text-sm">Tim kami memverifikasi dan memvalidasi laporan Anda</p>
            </div>
            <div class="step-card reveal reveal-delay-400">
                <div class="step-num" style="background:linear-gradient(135deg,#0891b2,#22d3ee)">3</div>
                <div class="step-icon-box"><svg class="w-10 h-10 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">Diproses</h3>
                <p class="text-gray-500 text-sm">Laporan ditindaklanjuti oleh pihak yang berwenang</p>
            </div>
            <div class="step-card reveal">
                <div class="step-num" style="background:linear-gradient(135deg,#16a34a,#4ade80)">4</div>
                <div class="step-icon-box"><svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">Selesai</h3>
                <p class="text-gray-500 text-sm">Terima notifikasi hasil penyelesaian dan feedback</p>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     CATEGORIES SECTION
═══════════════════════════════════════════════════════════ -->
<section class="py-20 md:py-28 bg-white" id="kategori">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-14 reveal">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-pink-50 rounded-full text-sm font-bold text-pink-700 mb-4 border border-pink-100">
                <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Kategori Laporan
            </div>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 mb-4">
                Apa yang Bisa <span style="color:#cc2c6b">Dilaporkan?</span>
            </h2>
            <p class="text-gray-500 max-w-2xl mx-auto text-base sm:text-lg">Berbagai kategori pengaduan untuk meningkatkan kualitas sekolah</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 w-full max-w-full overflow-hidden">
            <div class="cat-card reveal w-full" style="--cat-color:#cc2c6b">
                <div class="cat-icon-wrap flex-shrink-0"><svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div>
                <div class="flex-1 min-w-0 pr-2">
                    <h3 class="font-bold text-gray-900 text-sm sm:text-base mb-1 whitespace-normal break-words">Fasilitas Kelas</h3>
                    <p class="text-gray-500 text-xs sm:text-sm whitespace-normal leading-relaxed break-words">Kondisi ruang kelas, AC, proyektor, meja kursi</p>
                </div>
                <svg class="cat-arrow flex-shrink-0 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </div>

            <div class="cat-card reveal reveal-delay-200 w-full" style="--cat-color:#7c3aed">
                <div class="cat-icon-wrap flex-shrink-0"><svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg></div>
                <div class="flex-1 min-w-0 pr-2">
                    <h3 class="font-bold text-gray-900 text-sm sm:text-base mb-1 whitespace-normal break-words">Laboratorium</h3>
                    <p class="text-gray-500 text-xs sm:text-sm whitespace-normal leading-relaxed break-words">Peralatan lab, bahan praktikum, keamanan</p>
                </div>
                <svg class="cat-arrow flex-shrink-0 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </div>

            <div class="cat-card reveal reveal-delay-400 w-full" style="--cat-color:#2563eb">
                <div class="cat-icon-wrap flex-shrink-0"><svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg></div>
                <div class="flex-1 min-w-0 pr-2">
                    <h3 class="font-bold text-gray-900 text-sm sm:text-base mb-1 whitespace-normal break-words">Perpustakaan</h3>
                    <p class="text-gray-500 text-xs sm:text-sm whitespace-normal leading-relaxed break-words">Koleksi buku, ruang baca, sarana peminjaman</p>
                </div>
                <svg class="cat-arrow flex-shrink-0 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </div>

            <div class="cat-card reveal w-full" style="--cat-color:#ea580c">
                <div class="cat-icon-wrap flex-shrink-0"><svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg></div>
                <div class="flex-1 min-w-0 pr-2">
                    <h3 class="font-bold text-gray-900 text-sm sm:text-base mb-1 whitespace-normal break-words">Kantin & Toilet</h3>
                    <p class="text-gray-500 text-xs sm:text-sm whitespace-normal leading-relaxed break-words">Kebersihan, harga, kualitas makanan</p>
                </div>
                <svg class="cat-arrow flex-shrink-0 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </div>

            <div class="cat-card reveal reveal-delay-200 w-full" style="--cat-color:#dc2626">
                <div class="cat-icon-wrap flex-shrink-0"><svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11V4l4-2 4 2v7a13.92 13.92 0 00-1.85 6.94l-.055.09C13.009 17.798 12 14.517 12 11z"/></svg></div>
                <div class="flex-1 min-w-0 pr-2">
                    <h3 class="font-bold text-gray-900 text-sm sm:text-base mb-1 whitespace-normal break-words">Anti Bullying</h3>
                    <p class="text-gray-500 text-xs sm:text-sm whitespace-normal leading-relaxed break-words">Perundungan, intimidasi, kekerasan</p>
                </div>
                <svg class="cat-arrow flex-shrink-0 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </div>

            <div class="cat-card reveal reveal-delay-400 w-full" style="--cat-color:#16a34a">
                <div class="cat-icon-wrap flex-shrink-0"><svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                <div class="flex-1 min-w-0 pr-2">
                    <h3 class="font-bold text-gray-900 text-sm sm:text-base mb-1 whitespace-normal break-words">Administrasi</h3>
                    <p class="text-gray-500 text-xs sm:text-sm whitespace-normal leading-relaxed break-words">Pelayanan, biaya, dokumen</p>
                </div>
                <svg class="cat-arrow flex-shrink-0 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </div>

            <div class="cat-card reveal w-full" style="--cat-color:#f59e0b">
                <div class="cat-icon-wrap flex-shrink-0"><svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                <div class="flex-1 min-w-0 pr-2">
                    <h3 class="font-bold text-gray-900 text-sm sm:text-base mb-1 whitespace-normal break-words">Kedisiplinan Guru</h3>
                    <p class="text-gray-500 text-xs sm:text-sm whitespace-normal leading-relaxed break-words">Guru malas mengajar atau sering tidak hadir tanpa alasan</p>
                </div>
                <svg class="cat-arrow flex-shrink-0 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     FAQ SECTION
═══════════════════════════════════════════════════════════ -->
<section class="py-20 md:py-28 bg-slate-50" id="faq">
    <div class="max-w-4xl mx-auto px-6">
        <div class="text-center mb-14 reveal">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 rounded-full text-sm font-bold text-indigo-700 mb-4 border border-indigo-100">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Pusat Bantuan
            </div>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 mb-4">
                Pertanyaan yang Sering <span class="text-indigo-600">Ditanyakan</span>
            </h2>
            <p class="text-gray-500 text-base sm:text-lg">Temukan jawaban cepat untuk pertanyaan-pertanyaan umum seputar SIPS.</p>
        </div>

        <div class="space-y-4">
            <!-- FAQ 1 -->
            <div class="faq-card reveal">
                <button class="faq-btn w-full text-left px-6 py-5 flex items-center justify-between focus:outline-none" onclick="toggleFaq(this)">
                    <span class="font-bold text-gray-900 pr-4 transition-colors">Apakah identitas saya aman saat melapor?</span>
                    <span class="faq-icon flex-shrink-0 w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </span>
                </button>
                <div class="faq-content pr-6 pl-6 pb-0 overflow-hidden transition-all duration-300 max-h-0 opacity-0 relative z-[-1]">
                    <p class="text-gray-600 text-sm leading-relaxed pb-6">Tentu saja. SIPS menjamin kerahasiaan identitas setiap pelapor. Hanya petugas berwenang yang dapat melihat detail kontak Anda untuk keperluan tindak lanjut secara privat dan aman.</p>
                </div>
            </div>

            <!-- FAQ 2 -->
            <div class="faq-card reveal reveal-delay-200">
                <button class="faq-btn w-full text-left px-6 py-5 flex items-center justify-between focus:outline-none" onclick="toggleFaq(this)">
                    <span class="font-bold text-gray-900 pr-4 transition-colors">Berapa lama laporan saya akan diproses?</span>
                    <span class="faq-icon flex-shrink-0 w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </span>
                </button>
                <div class="faq-content pr-6 pl-6 pb-0 overflow-hidden transition-all duration-300 max-h-0 opacity-0 relative z-[-1]">
                    <p class="text-gray-600 text-sm leading-relaxed pb-6">Standar operasional prosedur kami adalah merespon setiap laporan maksimal 2x24 jam pada hari kerja aktif. Total waktu penyelesaian sangat bergantung pada tingkat bobot masalah lapangan.</p>
                </div>
            </div>

            <!-- FAQ 3 -->
            <div class="faq-card reveal reveal-delay-400">
                <button class="faq-btn w-full text-left px-6 py-5 flex items-center justify-between focus:outline-none" onclick="toggleFaq(this)">
                    <span class="font-bold text-gray-900 pr-4 transition-colors">Apakah saya bisa menarik/menghapus laporan?</span>
                    <span class="faq-icon flex-shrink-0 w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </span>
                </button>
                <div class="faq-content pr-6 pl-6 pb-0 overflow-hidden transition-all duration-300 max-h-0 opacity-0 relative z-[-1]">
                    <p class="text-gray-600 text-sm leading-relaxed pb-6">Apabila laporan sudah masuk tahap penyelidikan ("Diproses") atau "Selesai", maka laporan sudah direkam di buku catatan arsip dan tidak dapat dihapus. Anda hanya dapat membatalakan di awal fase "Menunggu".</p>
                </div>
            </div>

            <!-- FAQ 4 -->
            <div class="faq-card reveal">
                <button class="faq-btn w-full text-left px-6 py-5 flex items-center justify-between focus:outline-none" onclick="toggleFaq(this)">
                    <span class="font-bold text-gray-900 pr-4 transition-colors">Apa saja bukti sah yang wajib dilampirkan?</span>
                    <span class="faq-icon flex-shrink-0 w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </span>
                </button>
                <div class="faq-content pr-6 pl-6 pb-0 overflow-hidden transition-all duration-300 max-h-0 opacity-0 relative z-[-1]">
                    <p class="text-gray-600 text-sm leading-relaxed pb-6">Lampirkan foto nyata (maksimal 2MB per gambar), PDF pendukung, atau sekadar teks kronologi dengan detil lokasi dan waktu yang riil.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     CTA SECTION
═══════════════════════════════════════════════════════════ -->
<section class="cta-section py-20 md:py-28 relative overflow-hidden" id="cta-section">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-yellow-300 rounded-full blur-3xl"></div>
    </div>
    <div class="max-w-4xl mx-auto px-6 text-center relative z-10 reveal">
        <div class="mb-6 flex justify-center">
            <div class="cta-icon-pulse">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
        </div>
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white mb-6">Siap Membuat Perubahan?</h2>
        <p class="text-lg sm:text-xl text-white/70 mb-10 max-w-2xl mx-auto">Bergabunglah dengan ribuan siswa yang telah membuat sekolah menjadi tempat yang lebih baik. Suara Anda penting!</p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="/pengaduan/create" id="btn-cta-lapor" class="cta-btn-white group inline-flex items-center gap-3 px-8 sm:px-10 py-4 sm:py-5 rounded-2xl font-black text-base sm:text-lg">
                Mulai Lapor Sekarang
                <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
            <a href="/panduan" id="btn-cta-panduan" class="cta-btn-outline inline-flex items-center gap-2 px-8 sm:px-10 py-4 sm:py-5 rounded-2xl font-bold text-base sm:text-lg">
                Lihat Panduan
            </a>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     FOOTER
═══════════════════════════════════════════════════════════ -->
<footer class="footer-dark py-14 sm:py-16">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
            <div class="lg:col-span-1">
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ asset('storage/asset/img/logo.webp') }}" class="h-10" alt="SIPS Logo">
                    <div class="text-white font-bold text-xl">SIPS</div>
                </div>
                <p class="text-sm leading-relaxed text-gray-400 mb-5">Platform digital untuk menyuarakan aspirasi dan menciptakan lingkungan sekolah yang lebih baik.</p>
                <div class="flex gap-3">
                    <a href="#" class="footer-social" aria-label="Facebook">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="#" class="footer-social" aria-label="Instagram">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/></svg>
                    </a>
                </div>
            </div>
            <div>
                <h4 class="text-white font-bold mb-4 text-sm uppercase tracking-wider">Menu Cepat</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="/" class="footer-link">Home</a></li>
                    <li><a href="/pengaduan/create" class="footer-link">Buat Laporan</a></li>
                    <li><a href="/riwayat" class="footer-link">Riwayat</a></li>
                    <li><a href="/tentang" class="footer-link">Tentang Kami</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-bold mb-4 text-sm uppercase tracking-wider">Dukungan</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="/faq" class="footer-link">FAQ</a></li>
                    <li><a href="/panduan" class="footer-link">Panduan</a></li>
                    <li><a href="/kontak" class="footer-link">Kontak</a></li>
                    <li><a href="/kebijakan" class="footer-link">Kebijakan Privasi</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-bold mb-4 text-sm uppercase tracking-wider">Hubungi Kami</h4>
                <ul class="space-y-3 text-sm text-gray-400">
                    <li class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-pink-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>pengaduan_smkn1_bws@yahoo.com</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-pink-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>(0332) 431201</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-pink-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Jl. HOS. Cokroaminoto No. 110, Bondowoso, Jawa Timur</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-gray-500">
            <p>&copy; 2026 SMKN 1 Bondowoso. All rights reserved.</p>
            <div class="flex gap-6">
                <a href="/terms" class="footer-link">Syarat & Ketentuan</a>
                <a href="/privacy" class="footer-link">Privasi</a>
                <a href="/cookies" class="footer-link">Cookies</a>
            </div>
        </div>
    </div>
</footer>

<!-- ═══════════════════════════════════════════════════════════
     STYLES
═══════════════════════════════════════════════════════════ -->
<style>
    /* ──────── HERO ──────── */
    .hero-section {
        background: linear-gradient(160deg, #0f172a 0%, #1a1035 35%, #1e1b4b 70%, #0f172a 100%);
        position: relative;
    }

    .hero-bg-mesh {
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 80% 60% at 20% 80%, rgba(204,44,107,0.12) 0%, transparent 70%),
            radial-gradient(ellipse 60% 50% at 80% 20%, rgba(99,102,241,0.1) 0%, transparent 70%),
            radial-gradient(ellipse 50% 40% at 50% 50%, rgba(245,158,11,0.05) 0%, transparent 70%);
        pointer-events: none;
    }

    .hero-grain {
        position: absolute;
        inset: 0;
        opacity: 0.03;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
        pointer-events: none;
    }

    .hero-gradient-text {
        background: linear-gradient(135deg, #ec4899 0%, #a78bfa 50%, #818cf8 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .hero-pill {
        background: rgba(255,255,255,0.06);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 2px 12px rgba(0,0,0,0.2);
    }
    .hero-pill-text { color: rgba(255,255,255,0.8); }

    /* CTA buttons */
    .hero-btn-primary {
        background: linear-gradient(135deg, #cc2c6b 0%, #374151 100%);
        box-shadow: 0 8px 28px rgba(204,44,107,0.3), 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .hero-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 36px rgba(204,44,107,0.4), 0 4px 12px rgba(0,0,0,0.1);
    }

    .hero-btn-secondary {
        background: rgba(255,255,255,0.06);
        backdrop-filter: blur(8px);
        color: rgba(255,255,255,0.85);
        border: 1.5px solid rgba(255,255,255,0.15);
        box-shadow: 0 2px 12px rgba(0,0,0,0.1);
        transition: all 0.3s;
    }
    .hero-btn-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(204,44,107,0.15);
        border-color: rgba(255,255,255,0.35);
        color: #ffffff;
        background: rgba(255,255,255,0.1);
    }

    /* Hero stat chips */
    .hero-stat-chip {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 10px 18px;
        border-radius: 14px;
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.08);
    }
    .hero-stat-pink { background: rgba(204,44,107,0.1); }
    .hero-stat-indigo { background: rgba(99,102,241,0.1); }
    .hero-stat-emerald { background: rgba(16,185,129,0.1); }
    .hero-stat-num { font-size: 1.35rem; font-weight: 900; color: #ffffff; line-height: 1; }
    .hero-stat-lbl { font-size: 0.7rem; color: rgba(255,255,255,0.45); font-weight: 500; margin-top: 2px; }

    /* ──────── FLOATING LOGO & SPOTLIGHT ──────── */

    /* The wrapper is the positioning anchor — sized to the logo */
    .hero-logo-wrapper {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        /* Give vertical breathing room for the beam below */
        padding-bottom: 60px;
    }

    /* The logo itself */
    .hero-floating-logo {
        position: relative;
        z-index: 10;
        width: 200px;
        height: auto;
        filter: drop-shadow(0 10px 25px rgba(204,44,107,0.4));
        animation: float-logo-anim 4s ease-in-out infinite;
        transform-origin: center;
    }
    @media (min-width: 768px) {
        .hero-floating-logo { width: 260px; }
        .hero-logo-wrapper { padding-bottom: 80px; }
    }
    @media (min-width: 1024px) {
        .hero-floating-logo { width: 300px; }
    }

    @keyframes float-logo-anim {
        0%, 100% {
            transform: translateY(0) rotate(0deg);
            filter: drop-shadow(0 10px 25px rgba(204,44,107,0.4));
        }
        50% {
            transform: translateY(-22px) rotate(1.5deg);
            filter: drop-shadow(0 32px 35px rgba(204,44,107,0.55));
        }
    }



    /* Spotlight Base — glowing ellipse at the very bottom (light source) */
    .hero-spotlight {
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 180px;
        height: 24px;
        border-radius: 100%;
        background: radial-gradient(
            ellipse at center,
            rgba(204, 44, 107, 0.9) 0%,
            rgba(99, 102, 241, 0.5) 50%,
            transparent 80%
        );
        filter: blur(12px);
        pointer-events: none;
        z-index: 0;
        animation: pulse-spotlight-anim 4s ease-in-out infinite;
    }
    @media (min-width: 768px) {
        .hero-spotlight {
            width: 240px;
            height: 30px;
            filter: blur(16px);
        }
    }

    @keyframes pulse-spotlight-anim {
        0%, 100% { opacity: 0.9; }
        50% { opacity: 0.5; }
    }

    /* Glow rings */
    .hero-glow-ring {
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
    }
    .hero-glow-1 {
        width: 450px; height: 450px;
        background: radial-gradient(circle, rgba(204,44,107,0.12) 0%, transparent 70%);
        top: 50%; left: 50%; transform: translate(-50%, -50%);
        animation: pulse-glow 4s ease-in-out infinite;
    }
    .hero-glow-2 {
        width: 350px; height: 350px;
        background: radial-gradient(circle, rgba(99,102,241,0.1) 0%, transparent 70%);
        top: 50%; left: 50%; transform: translate(-50%, -50%);
        animation: pulse-glow 5s ease-in-out infinite 1.5s;
    }

    @keyframes pulse-glow {
        0%, 100% { opacity: 0.6; transform: translate(-50%, -50%) scale(1); }
        50% { opacity: 1; transform: translate(-50%, -50%) scale(1.1); }
    }

    /* Floating cards */
    .hero-float-card {
        position: absolute;
        display: flex;
        align-items: center;
        gap: 10px;
        background: rgba(255,255,255,0.06);
        backdrop-filter: blur(16px);
        padding: 12px 16px;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.2), 0 0 0 1px rgba(255,255,255,0.06);
        z-index: 10;
        border: 1px solid rgba(255,255,255,0.08);
    }
    .hero-float-card-top {
        top: 10%;
        left: -5%;
        animation: float-y 4s ease-in-out infinite;
    }
    .hero-float-card-bottom {
        bottom: 15%;
        right: -5%;
        animation: float-y 5s ease-in-out infinite 1.5s;
    }
    .hero-float-icon {
        width: 40px; height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    @keyframes float-y {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-14px); }
    }

    /* ──────── STATS BANNER ──────── */
    .stats-banner {
        background: linear-gradient(135deg, #cc2c6b 0%, #7c3aed 50%, #374151 100%);
        position: relative;
        overflow: hidden;
    }
    .stats-banner::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    /* ──────── FEATURE CARDS ──────── */
    .feat-card {
        background: white;
        padding: 28px;
        border-radius: 20px;
        border: 1.5px solid #f1f5f9;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 1px 8px rgba(0,0,0,0.03);
        position: relative;
        overflow: hidden;
    }
    .feat-card::after {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 3px;
        background: var(--tw-gradient-from, attr(data-accent));
        opacity: 0;
        transition: opacity 0.35s;
    }
    .feat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 48px rgba(0,0,0,0.08);
        border-color: transparent;
    }
    .feat-card:hover::after { opacity: 1; }
    .feat-icon-wrap {
        width: 56px; height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        transition: transform 0.3s;
    }
    .feat-card:hover .feat-icon-wrap { transform: scale(1.1); }

    /* ──────── HOW IT WORKS ──────── */
    .how-section {
        background: linear-gradient(160deg, #0f172a 0%, #1e1b4b 40%, #1e293b 100%);
    }

    .step-card {
        background: white;
        padding: 32px 24px;
        border-radius: 20px;
        text-align: center;
        position: relative;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 20px rgba(0,0,0,0.12);
    }
    .step-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 24px 60px rgba(0,0,0,0.2);
    }
    .step-num {
        position: absolute;
        top: -16px; left: 50%; transform: translateX(-50%);
        width: 36px; height: 36px;
        border-radius: 50%;
        color: white;
        font-weight: 900;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .step-icon-box {
        width: 80px; height: 80px;
        background: #f8fafc;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 20px auto;
        transition: transform 0.3s;
    }
    .step-card:hover .step-icon-box { transform: rotate(6deg) scale(1.05); }

    /* ──────── CATEGORIES ──────── */
    .cat-card {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 18px 22px;
        border-radius: 16px;
        border: 1.5px solid #f1f5f9;
        background: #fafafa;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .cat-card:hover {
        background: white;
        transform: translateX(6px);
        box-shadow: 0 8px 28px rgba(0,0,0,0.06);
        border-color: var(--cat-color, #cc2c6b);
    }
    .cat-icon-wrap {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 46px; height: 46px;
        border-radius: 14px;
        background: rgba(0,0,0,0.03);
        flex-shrink: 0;
        transition: all 0.3s;
        color: #9ca3af;
    }
    .cat-card:hover .cat-icon-wrap {
        background: color-mix(in srgb, var(--cat-color, #cc2c6b) 10%, transparent);
        color: var(--cat-color, #cc2c6b);
    }
    .cat-arrow {
        width: 20px; height: 20px;
        color: #d1d5db;
        margin-left: auto;
        flex-shrink: 0;
        transition: all 0.25s;
    }
    .cat-card:hover .cat-arrow {
        transform: translateX(4px);
        color: var(--cat-color, #cc2c6b);
    }

    /* ──────── FAQ ──────── */
    .faq-card {
        background: white;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }
    .faq-card:hover {
        border-color: #e0e7ff;
        box-shadow: 0 4px 20px rgba(79, 70, 229, 0.05);
    }
    .faq-card.open {
        border-color: #6366f1;
        box-shadow: 0 10px 30px rgba(99, 102, 241, 0.1);
    }
    .faq-card.open .faq-icon {
        transform: rotate(180deg);
        background: #6366f1;
        color: white;
    }
    .faq-card.open .font-bold {
        color: #4f46e5;
    }

    /* ──────── CTA ──────── */
    .cta-section {
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 40%, #cc2c6b 100%);
    }
    .cta-icon-pulse {
        width: 72px; height: 72px;
        background: linear-gradient(135deg, #cc2c6b, #ec4899);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: pulse-ring 2s ease-in-out infinite;
        box-shadow: 0 0 30px rgba(204,44,107,0.4);
    }
    @keyframes pulse-ring {
        0%, 100% { box-shadow: 0 0 20px rgba(204,44,107,0.4); }
        50% { box-shadow: 0 0 40px rgba(204,44,107,0.6), 0 0 60px rgba(204,44,107,0.2); }
    }
    .cta-btn-white {
        background: white;
        color: #cc2c6b;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        transition: all 0.3s;
    }
    .cta-btn-white:hover {
        transform: scale(1.03) translateY(-2px);
        box-shadow: 0 12px 36px rgba(0,0,0,0.2);
    }
    .cta-btn-outline {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(8px);
        color: white;
        border: 1.5px solid rgba(255,255,255,0.25);
        transition: all 0.3s;
    }
    .cta-btn-outline:hover {
        background: rgba(255,255,255,0.2);
        border-color: rgba(255,255,255,0.5);
    }

    /* ──────── FOOTER ──────── */
    .footer-dark { background: #0a0a0f; color: #6b7280; }
    .footer-social {
        width: 36px; height: 36px;
        background: rgba(255,255,255,0.06);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
        transition: all 0.25s;
    }
    .footer-social:hover { background: #cc2c6b; color: white; }
    .footer-link { color: #6b7280; transition: color 0.2s; }
    .footer-link:hover { color: #ec4899; }

    /* ──────── SCROLL ANIMATIONS ──────── */
    .reveal {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.8s cubic-bezier(0.5, 0, 0, 1);
    }
    .reveal.active {
        opacity: 1;
        transform: none;
    }
    .reveal-delay-200 { transition-delay: 0.2s; }
    .reveal-delay-400 { transition-delay: 0.4s; }

    /* ──────── RESPONSIVE ──────── */
    @media (max-width: 1024px) {
        .hero-float-card-top { left: 0; top: 5%; }
        .hero-float-card-bottom { right: 0; bottom: 5%; }
        .hero-canvas-wrap { max-width: 400px; }
    }
    @media (max-width: 768px) {
        .hero-float-card { display: none; }
        .hero-glow-ring { display: none; }
        .hero-canvas-wrap { max-width: 320px; }
    }

    html { scroll-behavior: smooth; }
</style>

<!-- ═══════════════════════════════════════════════════════════
     SCRIPTS
═══════════════════════════════════════════════════════════ -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Reveal Animation (Intersection Observer for perf)
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -60px 0px' });

    document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

    // 2. FAQ Toggle Logic
    window.toggleFaq = function(btn) {
        const card = btn.parentElement;
        const content = card.querySelector('.faq-content');
        
        // Auto-close open accordions
        document.querySelectorAll('.faq-card').forEach(otherCard => {
            if (otherCard !== card && otherCard.classList.contains('open')) {
                otherCard.classList.remove('open');
                const otherContent = otherCard.querySelector('.faq-content');
                otherContent.style.maxHeight = '0px';
                otherContent.style.opacity = '0';
                otherContent.style.zIndex = '-1';
            }
        });

        // Toggle states
        if (card.classList.contains('open')) {
            card.classList.remove('open');
            content.style.maxHeight = '0px';
            content.style.opacity = '0';
            content.style.zIndex = '-1';
        } else {
            card.classList.add('open');
            content.style.zIndex = '1';
            content.style.opacity = '1';
            content.style.maxHeight = content.scrollHeight + 'px';
        }
    }

    // 3. Counter Animation
    const counters = document.querySelectorAll('.counter');
    const speed = 40;
    let countersAnimated = new Set();

    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !countersAnimated.has(entry.target)) {
                countersAnimated.add(entry.target);
                const counter = entry.target;
                const target = +counter.getAttribute('data-target');
                const updateCount = () => {
                    const count = +counter.innerText;
                    const inc = target / speed;
                    if (count < target) {
                        counter.innerText = Math.ceil(count + inc);
                        requestAnimationFrame(updateCount);
                    } else {
                        counter.innerText = target;
                    }
                };
                updateCount();
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(c => counterObserver.observe(c));

    // 4. Smooth 3D Model Scroll (FLIP Animation)
    const canvasWrap = document.getElementById('three-hero-canvas');
    if (canvasWrap) {
        let flightTimeout = null;

        window.addEventListener('scroll', () => {
            const isScrolledOut = window.scrollY > 400; 
            const isFloating = canvasWrap.classList.contains('floating-corner');
            const isReturning = canvasWrap.dataset.returning === 'true';
            
            // BUG FIX: Jika scroll super cepat bolak-balik, kita izinkan intercept animasi!
            if (isScrolledOut && (!isFloating || isReturning)) {
                
                // Batalkan proses 'pulang' jika sedang terjadi
                canvasWrap.dataset.returning = 'false';
                clearTimeout(flightTimeout);

                // 1) Dapatkan koordinat awal (posisi elemen tepat detik ini)
                const startRect = canvasWrap.getBoundingClientRect();
                
                // Amankan dari negative value ekstrem akibat scroll instan menggunakan scrollbar
                let safeTop = startRect.top;
                if (safeTop < -800) safeTop = -800; 
                
                canvasWrap.style.transition = 'none';
                canvasWrap.classList.add('floating-corner');
                canvasWrap.style.left = startRect.left + 'px';
                canvasWrap.style.top = safeTop + 'px';
                canvasWrap.style.width = startRect.width + 'px';
                canvasWrap.style.height = startRect.height + 'px';
                canvasWrap.style.transform = 'none'; 
                
                void canvasWrap.offsetHeight; // Force reflow
                
                // 2) Tentukan posisi target (pojok kanan bawah)
                const isMobile = window.innerWidth <= 768;
                const targetW = isMobile ? 160 : 250;
                const targetH = isMobile ? 160 : 250;
                const gap = isMobile ? 16 : 24;
                const destLeft = window.innerWidth - targetW - gap;
                const destTop = window.innerHeight - targetH - gap;
                
                // 3) Terbang!
                canvasWrap.style.transition = 'all 0.6s cubic-bezier(0.22, 1, 0.36, 1)';
                canvasWrap.style.left = destLeft + 'px';
                canvasWrap.style.top = destTop + 'px';
                canvasWrap.style.width = targetW + 'px';
                canvasWrap.style.height = targetH + 'px';
                canvasWrap.style.minHeight = targetH + 'px';
                
                setTimeout(() => window.dispatchEvent(new Event('resize')), 20);
                
            } else if (!isScrolledOut && isFloating && !isReturning) {
                // Tandai bahwa elemen sedang dalam proses 'pulang'
                canvasWrap.dataset.returning = 'true';
                clearTimeout(flightTimeout);

                // 1) Catat posisi di pojok
                const cornerRect = canvasWrap.getBoundingClientRect();
                
                // Kalkulasi letak absolut di tengah
                canvasWrap.classList.remove('floating-corner');
                canvasWrap.style.transition = 'none';
                canvasWrap.style.left = '50%';
                canvasWrap.style.top = '50%';
                canvasWrap.style.width = '100%';
                canvasWrap.style.height = '100%';
                canvasWrap.style.minHeight = '700px';
                canvasWrap.style.transform = 'translate(-50%, -50%)';
                
                const centerRect = canvasWrap.getBoundingClientRect();
                
                // 2) Gembok ulang di pojok tanpa transisi
                canvasWrap.classList.add('floating-corner'); 
                canvasWrap.style.left = cornerRect.left + 'px';
                canvasWrap.style.top = cornerRect.top + 'px';
                canvasWrap.style.width = cornerRect.width + 'px';
                canvasWrap.style.height = cornerRect.height + 'px';
                canvasWrap.style.minHeight = cornerRect.height + 'px';
                canvasWrap.style.transform = 'none';
                
                void canvasWrap.offsetHeight; // Force reflow
                
                // 3) Terbang balik!
                canvasWrap.style.transition = 'all 0.6s cubic-bezier(0.22, 1, 0.36, 1)';
                canvasWrap.style.left = centerRect.left + 'px';
                canvasWrap.style.top = centerRect.top + 'px';
                canvasWrap.style.width = centerRect.width + 'px';
                canvasWrap.style.height = centerRect.height + 'px';
                canvasWrap.style.minHeight = '700px';
                
                setTimeout(() => window.dispatchEvent(new Event('resize')), 20);
                
                // Buang class fixed HANYA jika proses ini tuntas (tdk dibatalkan timeout)
                flightTimeout = setTimeout(() => {
                    if (canvasWrap.dataset.returning === 'true') {
                        canvasWrap.classList.remove('floating-corner');
                        canvasWrap.dataset.returning = 'false';
                        
                        canvasWrap.style.transition = 'all 0.6s cubic-bezier(0.22, 1, 0.36, 1)';
                        canvasWrap.style.left = '50%';
                        canvasWrap.style.top = '50%';
                        canvasWrap.style.width = '100%';
                        canvasWrap.style.height = '100%';
                        canvasWrap.style.transform = 'translate(-50%, -50%)';
                        
                        setTimeout(() => window.dispatchEvent(new Event('resize')), 20);
                    }
                }, 600);
            }
        }, { passive: true });
    }
});
</script>

@endsection
