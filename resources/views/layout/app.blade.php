<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIPS - Sarana Informasi Pengaduan Sekolah')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO & PWA Meta Tags --}}
    <meta name="theme-color" content="#cc2c6b">
    <meta name="description" content="SIPS - Sarana Informasi Pengaduan Sekolah yang mudah, cepat, dan transparan.">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SIPS">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192x192.png">

    {{-- Google Fonts — diload via link tag agar kompatibel di mobile --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* =====================================================
           FONT — Poppins dari Google Fonts
        ===================================================== */
        * { font-family: 'Poppins', sans-serif; }

        /* =====================================================
           CSS VARIABLES — Warna global agar konsisten
        ===================================================== */
        :root {
            --nav-primary: #cc2c6b;
            --nav-primary-rgb: 204, 44, 107;
            --nav-dark: #0f172a;
            --nav-dark-rgb: 15, 23, 42;
            --nav-accent: #ec4899;
            --nav-transition: 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* =====================================================
           NAVBAR — Container utama
        ===================================================== */
        .gnav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            transition: background var(--nav-transition),
                        backdrop-filter var(--nav-transition),
                        border-color var(--nav-transition),
                        box-shadow var(--nav-transition);
        }

        /* ── State 1: Transparan (saat di atas halaman, homepage saja) ── */
        .gnav--transparent {
            background: transparent;
            border-bottom: 1px solid transparent;
            box-shadow: none;
        }
        /* Teks putih di state transparan */
        .gnav--transparent .gnav__link { color: rgba(255,255,255,0.85); }
        .gnav--transparent .gnav__link:hover { color: #ffffff; }
        .gnav--transparent .gnav__link--active { color: #ffffff; }
        .gnav--transparent .gnav__login-btn {
            color: rgba(255,255,255,0.9);
            border-color: rgba(255,255,255,0.3);
        }
        .gnav--transparent .gnav__login-btn:hover {
            background: rgba(255,255,255,0.12);
            border-color: rgba(255,255,255,0.5);
            color: #ffffff;
        }
        .gnav--transparent .gnav__hamburger-line { background: #ffffff; }

        /* ── State 2: Frosted glass gelap (setelah scroll melewati threshold) ── */
        .gnav--scrolled {
            background: rgba(var(--nav-dark-rgb), 0.78);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border-bottom: 1px solid rgba(255,255,255,0.06);
            box-shadow:
                0 1px 2px rgba(0,0,0,0.12),
                0 8px 32px rgba(var(--nav-primary-rgb), 0.08);
        }
        /* Teks putih di state scrolled */
        .gnav--scrolled .gnav__link { color: rgba(255,255,255,0.75); }
        .gnav--scrolled .gnav__link:hover { color: #ffffff; }
        .gnav--scrolled .gnav__link--active { color: #ffffff; }
        .gnav--scrolled .gnav__login-btn {
            color: rgba(255,255,255,0.85);
            border-color: rgba(255,255,255,0.2);
        }
        .gnav--scrolled .gnav__login-btn:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.4);
            color: #ffffff;
        }
        .gnav--scrolled .gnav__hamburger-line { background: #ffffff; }

        /* ── State non-homepage: Selalu tampil background glass ── */
        .gnav--solid {
            background: rgba(var(--nav-dark-rgb), 0.82);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border-bottom: 1px solid rgba(255,255,255,0.05);
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        .gnav--solid .gnav__link { color: rgba(255,255,255,0.75); }
        .gnav--solid .gnav__link:hover { color: #ffffff; }
        .gnav--solid .gnav__link--active { color: #ffffff; }
        .gnav--solid .gnav__login-btn {
            color: rgba(255,255,255,0.85);
            border-color: rgba(255,255,255,0.2);
        }
        .gnav--solid .gnav__login-btn:hover {
            background: rgba(255,255,255,0.1);
            color: #ffffff;
        }
        .gnav--solid .gnav__hamburger-line { background: #ffffff; }

        /* =====================================================
           NAVBAR — Inner layout (3 kolom: logo | center | right)
        ===================================================== */
        .gnav__inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 80px;
            transition: height var(--nav-transition);
        }
        .gnav--scrolled .gnav__inner { height: 64px; }

        /* =====================================================
           LOGO — Kiri
        ===================================================== */
        .gnav__logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            flex-shrink: 0;
            transition: transform 0.3s, opacity 0.3s;
        }
        /* Efek hover: sedikit naik + kilau */
        .gnav__logo:hover {
            transform: translateY(-2px);
        }

        .gnav__logo-img--desktop {
            height: 52px;
            transition: height var(--nav-transition), filter 0.3s;
        }
        .gnav--scrolled .gnav__logo-img--desktop { height: 40px; }
        /* Logo cerah di state transparan & scrolled (background gelap) */
        .gnav--transparent .gnav__logo-img--desktop,
        .gnav--scrolled .gnav__logo-img--desktop,
        .gnav--solid .gnav__logo-img--desktop {
            filter: brightness(0) invert(1);
        }

        .gnav__logo-img--mobile {
            height: 34px;
            display: none;
        }
        .gnav--transparent .gnav__logo-img--mobile,
        .gnav--scrolled .gnav__logo-img--mobile,
        .gnav--solid .gnav__logo-img--mobile {
            filter: brightness(0) invert(1);
        }

        /* =====================================================
           NAV LINKS — Tengah (Desktop)
        ===================================================== */
        .gnav__center {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .gnav__link {
            position: relative;
            padding: 8px 18px;
            font-size: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            border-radius: 10px;
            transition: color 0.25s, background 0.25s;
        }

        /* Garis bawah animasi — muncul dari tengah ke samping */
        .gnav__link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%) scaleX(0);
            width: 24px;
            height: 2px;
            border-radius: 2px;
            background: var(--nav-primary);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .gnav__link:hover::after {
            transform: translateX(-50%) scaleX(1);
        }

        /* Active link: garis bawah permanen + dot kecil */
        .gnav__link--active::after {
            transform: translateX(-50%) scaleX(1);
            background: var(--nav-accent);
        }
        .gnav__link--active::before {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: var(--nav-accent);
        }

        /* =====================================================
           AKSI — Kanan (Login + CTA)
        ===================================================== */
        .gnav__right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Tombol Login — Ghost / Outline style */
        .gnav__login-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            border-radius: 12px;
            border: 1.5px solid rgba(255,255,255,0.25);
            background: transparent;
            transition: all 0.3s;
        }

        /* Tombol CTA — Solid gradient */
        .gnav__cta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            font-size: 0.75rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--nav-primary) 0%, #9f1239 100%);
            box-shadow: 0 4px 18px rgba(var(--nav-primary-rgb), 0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        /* Efek shine saat hover */
        .gnav__cta::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.18), transparent);
            transition: left 0.5s ease;
        }
        .gnav__cta:hover::before { left: 100%; }
        .gnav__cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(var(--nav-primary-rgb), 0.4);
        }

        /* =====================================================
           HAMBURGER — Tombol mobile (3 garis → X)
        ===================================================== */
        .gnav__hamburger {
            display: none;
            width: 44px; height: 44px;
            border-radius: 12px;
            border: none;
            background: transparent;
            cursor: pointer;
            position: relative;
            transition: background 0.2s;
            flex-shrink: 0;
        }
        .gnav__hamburger:hover { background: rgba(255,255,255,0.08); }

        .gnav__hamburger-line {
            position: absolute;
            left: 50%;
            width: 22px; height: 2px;
            border-radius: 2px;
            transform: translateX(-50%);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .gnav__hamburger-line:nth-child(1) { top: 14px; }
        .gnav__hamburger-line:nth-child(2) { top: 21px; width: 16px; }
        .gnav__hamburger-line:nth-child(3) { top: 28px; }

        /* Animasi morph: hamburger → silang (X) */
        .gnav__hamburger--active .gnav__hamburger-line:nth-child(1) {
            top: 21px;
            transform: translateX(-50%) rotate(45deg);
            background: var(--nav-primary) !important;
        }
        .gnav__hamburger--active .gnav__hamburger-line:nth-child(2) {
            opacity: 0;
            transform: translateX(-50%) translateX(12px);
        }
        .gnav__hamburger--active .gnav__hamburger-line:nth-child(3) {
            top: 21px;
            transform: translateX(-50%) rotate(-45deg);
            background: var(--nav-primary) !important;
        }

        /* =====================================================
           MOBILE MENU — Fullscreen overlay glassmorphism gelap
        ===================================================== */
        .gnav__mobile-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            z-index: 98;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.4s;
        }
        .gnav__mobile-overlay--visible {
            opacity: 1;
            pointer-events: auto;
        }

        .gnav__mobile-panel {
            position: fixed;
            top: 0; right: 0;
            width: min(340px, 88vw);
            height: 100dvh;
            /* Glassmorphism gelap sesuai spec */
            background: rgba(var(--nav-dark-rgb), 0.88);
            backdrop-filter: blur(32px) saturate(200%);
            -webkit-backdrop-filter: blur(32px) saturate(200%);
            z-index: 99;
            padding: 0;
            transform: translateX(100%);
            transition: transform 0.45s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: -12px 0 48px rgba(0,0,0,0.25);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }
        .gnav__mobile-panel--open {
            transform: translateX(0);
        }

        /* Header mobile panel */
        .gnav__mobile-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 24px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .gnav__mobile-brand {
            font-size: 1.1rem;
            font-weight: 800;
            color: white;
            letter-spacing: -0.02em;
        }
        .gnav__mobile-brand span { color: var(--nav-primary); }



        /* Links mobile — staggered reveal */
        .gnav__mobile-body {
            padding: 16px 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .gnav__mobile-links {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .gnav__mobile-link {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            border-radius: 14px;
            font-size: 0.9rem;
            font-weight: 600;
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            transition: all 0.25s;
            /* Staggered reveal: awal tersembunyi */
            opacity: 0;
            transform: translateY(-12px);
        }
        /* Saat panel terbuka, link muncul bertahap */
        .gnav__mobile-panel--open .gnav__mobile-link {
            opacity: 1;
            transform: translateY(0);
        }
        .gnav__mobile-panel--open .gnav__mobile-link:nth-child(1) { transition-delay: 0.08s; }
        .gnav__mobile-panel--open .gnav__mobile-link:nth-child(2) { transition-delay: 0.14s; }
        .gnav__mobile-panel--open .gnav__mobile-link:nth-child(3) { transition-delay: 0.20s; }
        .gnav__mobile-panel--open .gnav__mobile-link:nth-child(4) { transition-delay: 0.26s; }
        .gnav__mobile-panel--open .gnav__mobile-link:nth-child(5) { transition-delay: 0.32s; }

        .gnav__mobile-link:hover {
            background: rgba(255,255,255,0.06);
            color: #ffffff;
        }
        .gnav__mobile-link--active {
            background: rgba(var(--nav-primary-rgb), 0.12);
            color: var(--nav-primary);
        }
        .gnav__mobile-link--active:hover {
            color: var(--nav-accent);
        }

        .gnav__mobile-link svg {
            width: 20px !important;
            height: 20px !important;
            flex-shrink: 0;
            opacity: 0.7;
        }
        .gnav__mobile-link--active svg { opacity: 1; }

        /* CTA mobile */
        .gnav__mobile-cta {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
            padding: 14px 20px;
            border-radius: 14px;
            font-size: 0.9rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
            background: linear-gradient(135deg, var(--nav-primary), #9f1239);
            box-shadow: 0 4px 20px rgba(var(--nav-primary-rgb), 0.3);
            transition: all 0.3s;
            /* Stagger juga */
            opacity: 0;
            transform: translateY(-12px);
        }
        .gnav__mobile-panel--open .gnav__mobile-cta {
            opacity: 1;
            transform: translateY(0);
            transition-delay: 0.36s;
        }
        .gnav__mobile-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(var(--nav-primary-rgb), 0.4);
        }

        /* Footer mobile */
        .gnav__mobile-footer {
            margin-top: auto;
            padding: 20px 24px;
            border-top: 1px solid rgba(255,255,255,0.06);
            text-align: center;
        }
        .gnav__mobile-footer p {
            font-size: 0.7rem;
            color: rgba(255,255,255,0.25);
        }

        /* =====================================================
           PAGE LOAD — Animasi muncul bertahap
        ===================================================== */
        .gnav__logo,
        .gnav__center .gnav__link,
        .gnav__right > * {
            opacity: 0;
            transform: translateY(-10px);
            animation: navReveal 0.5s forwards;
        }
        /* Logo muncul pertama */
        .gnav__logo { animation-delay: 0.1s; }
        /* Nav links muncul satu per satu */
        .gnav__center .gnav__link:nth-child(1) { animation-delay: 0.18s; }
        .gnav__center .gnav__link:nth-child(2) { animation-delay: 0.24s; }
        .gnav__center .gnav__link:nth-child(3) { animation-delay: 0.30s; }
        .gnav__center .gnav__link:nth-child(4) { animation-delay: 0.36s; }
        /* Tombol aksi muncul terakhir */
        .gnav__right > *:nth-child(1) { animation-delay: 0.40s; }
        .gnav__right > *:nth-child(2) { animation-delay: 0.48s; }
        /* Hamburger juga */
        .gnav__hamburger { animation: navReveal 0.5s 0.2s forwards; opacity: 0; transform: translateY(-10px); }

        @keyframes navReveal {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* =====================================================
           BREADCRUMB
        ===================================================== */
        .breadcrumb-active { color: var(--nav-primary); font-weight: 600; }
        .breadcrumb-link:hover { color: var(--nav-primary); }

        /* =====================================================
           RESPONSIVE
        ===================================================== */
        @media (max-width: 768px) {
            .gnav__center,
            .gnav__right { display: none; }
            .gnav__hamburger { display: block; }
            .gnav__logo-img--desktop { display: none; }
            .gnav__logo-img--mobile { display: block; }
            .gnav__inner { height: 64px; }
            .gnav--scrolled .gnav__inner { height: 56px; }
        }

        @media (min-width: 769px) {
            .gnav__mobile-panel,
            .gnav__mobile-overlay { display: none !important; }
        }

        /* =====================================================
           PAGE TRANSITION OVERLAY
        ===================================================== */
        #page-transition {
            position: fixed;
            inset: 0;
            background-color: #ffffff;
            z-index: 99999;
            pointer-events: none;
            opacity: 1;
            transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #page-transition.loaded {
            opacity: 0;
        }
    </style>
    @stack('head_scripts')
</head>
<body class="font-sans bg-white text-gray-900">

    {{-- ═══ PAGE TRANSITION OVERLAY ═══ --}}
    <div id="page-transition"></div>

    {{-- ═══════════════════════════════════════════════════
         NAVBAR — Transparan + Scroll glassmorphism
    ═══════════════════════════════════════════════════ --}}
    <nav class="gnav {{ Request::is('/') ? 'gnav--transparent' : 'gnav--solid' }}" id="gnav" role="navigation" aria-label="Navigasi utama">
        <div class="gnav__inner">

            {{-- ── Kiri: Logo ── --}}
            <a href="/" class="gnav__logo" aria-label="SIPS - Kembali ke beranda">
                <img src="{{ asset('storage/asset/img/logo.webp') }}" class="gnav__logo-img--desktop" alt="SIPS Logo">
                <img src="{{ asset('storage/asset/img/logo.webp') }}" class="gnav__logo-img--mobile" alt="SIPS Logo">
            </a>

            {{-- ── Tengah: Link Navigasi (Desktop) ── --}}
            <div class="gnav__center">
                <a href="/" class="gnav__link {{ Request::is('/') ? 'gnav__link--active' : '' }}">Home</a>
                <a href="/panduan" class="gnav__link {{ Request::is('panduan') ? 'gnav__link--active' : '' }}">Panduan</a>
                <a href="{{ Request::is('/') ? '#fitur' : url('/#fitur') }}" class="gnav__link">Fitur</a>
            </div>

            {{-- ── Kanan: Auth + CTA (Desktop) ── --}}
            <div class="gnav__right">
                @auth
                    <a href="{{ route('dashboard') }}" class="gnav__login-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="gnav__login-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Masuk
                    </a>
                @endauth

                <a href="/pengaduan/create" class="gnav__cta" id="gnav-cta">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Buat Laporan
                </a>
            </div>

            {{-- ── Hamburger (Mobile) ── --}}
            <button class="gnav__hamburger" id="gnav-hamburger" type="button" aria-label="Buka menu navigasi">
                <span class="gnav__hamburger-line"></span>
                <span class="gnav__hamburger-line"></span>
                <span class="gnav__hamburger-line"></span>
            </button>
        </div>
    </nav>

    {{-- ═══ MOBILE OVERLAY ═══ --}}
    <div class="gnav__mobile-overlay" id="gnav-overlay"></div>

    {{-- ═══ MOBILE SLIDE-OUT PANEL ═══ --}}
    <div class="gnav__mobile-panel" id="gnav-mobile">
        {{-- Header --}}
        <div class="gnav__mobile-header">
            <div class="gnav__mobile-brand"><span>S</span>IPS</div>
        </div>

        {{-- Body: links --}}
        <div class="gnav__mobile-body">
            <div class="gnav__mobile-links">
                <a href="/" class="gnav__mobile-link {{ Request::is('/') ? 'gnav__mobile-link--active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Home
                </a>

                <a href="/panduan" class="gnav__mobile-link {{ Request::is('panduan') ? 'gnav__mobile-link--active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    Panduan
                </a>

                @auth
                <a href="{{ route('dashboard') }}" class="gnav__mobile-link {{ Request::routeIs('dashboard') ? 'gnav__mobile-link--active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Dashboard
                </a>
                @else
                <a href="{{ route('login') }}" class="gnav__mobile-link {{ Request::routeIs('login') ? 'gnav__mobile-link--active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Masuk
                </a>
                @endauth
            </div>

            {{-- CTA mobile --}}
            <a href="/pengaduan/create" class="gnav__mobile-cta">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Buat Laporan
            </a>
        </div>

        {{-- Footer mobile --}}
        <div class="gnav__mobile-footer">
            <p>&copy; 2026 SMKN 1 Bondowoso</p>
        </div>
    </div>

    {{-- ═══ KONTEN HALAMAN ═══ --}}
    <div class="{{ Request::is('/') ? '' : 'pt-20' }}">
        @if (!Request::is('/'))
        <div class="max-w-screen-xl mx-auto px-6 py-4">
            <nav aria-label="Breadcrumb">
                <ol class="flex items-center gap-2 text-sm text-gray-500">
                    <li>
                        <a href="/" class="breadcrumb-link hover:text-pink-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                        </a>
                    </li>
                    <li class="text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </li>
                    <li class="breadcrumb-active">@yield('title', 'Laman')</li>
                </ol>
            </nav>
        </div>
        @endif

        @if (Request::is('/'))
            <main class="min-h-screen">
                @yield('content')
            </main>
        @else
            <main class="max-w-screen-xl mx-auto px-6 min-h-screen">
                @yield('content')
            </main>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════
         JAVASCRIPT — Scroll behavior + Mobile menu
    ═══════════════════════════════════════════════════ --}}
    <script>
    (function() {
        'use strict';

        const gnav = document.getElementById('gnav');
        const hamburger = document.getElementById('gnav-hamburger');
        const mobilePanel = document.getElementById('gnav-mobile');
        const overlay = document.getElementById('gnav-overlay');
        const isHomepage = gnav.classList.contains('gnav--transparent');

        // Threshold scroll: 60-80px sesuai spec
        const SCROLL_THRESHOLD = 70;

        // ── Deteksi scroll & toggle state navbar ──
        function handleScroll() {
            const scrolled = window.scrollY > SCROLL_THRESHOLD;

            if (isHomepage) {
                // Di homepage: transparan → frosted glass gelap
                gnav.classList.toggle('gnav--scrolled', scrolled);
                gnav.classList.toggle('gnav--transparent', !scrolled);
            } else {
                // Di halaman lain: selalu solid, tambah efek saat scroll
                gnav.classList.toggle('gnav--scrolled', scrolled);
            }
        }
        window.addEventListener('scroll', handleScroll, { passive: true });
        handleScroll(); // Cek posisi awal saat halaman dimuat

        // ── Buka menu mobile ──
        function openMobile() {
            mobilePanel.classList.add('gnav__mobile-panel--open');
            overlay.classList.add('gnav__mobile-overlay--visible');
            hamburger.classList.add('gnav__hamburger--active');
            document.body.style.overflow = 'hidden'; // Kunci scroll body
        }

        // ── Tutup menu mobile ──
        function closeMobile() {
            mobilePanel.classList.remove('gnav__mobile-panel--open');
            overlay.classList.remove('gnav__mobile-overlay--visible');
            hamburger.classList.remove('gnav__hamburger--active');
            document.body.style.overflow = ''; // Buka kunci scroll body
        }

        // Event listener: klik hamburger untuk toggle
        hamburger.addEventListener('click', function() {
            const isOpen = mobilePanel.classList.contains('gnav__mobile-panel--open');
            isOpen ? closeMobile() : openMobile();
        });

        // Tutup saat klik overlay di luar panel
        overlay.addEventListener('click', closeMobile);

        // Tutup saat tekan tombol Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeMobile();
        });

        // Tutup saat klik link di dalam menu mobile
        mobilePanel.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', closeMobile);
        });

        // ── Smooth scroll untuk link anchor (#fitur, dll) ──
        document.querySelectorAll('.gnav__link[href^="#"], .gnav__mobile-link[href^="#"]').forEach(function(link) {
            link.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                const targetEl = document.querySelector(targetId);
                if (targetEl) {
                    e.preventDefault();
                    closeMobile();
                    targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // ── Transisi Halaman (Page Transition) ──
        const pageTransition = document.getElementById('page-transition');
        
        // Hilangkan layar putih (fade in) setelah halaman dimuat
        window.addEventListener('load', function() {
            setTimeout(() => {
                pageTransition.classList.add('loaded');
            }, 50);
        });

        // Munculkan layar putih (fade out) sebelum berpindah ke halaman lain
        document.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                const target = this.getAttribute('target');
                
                // Acuhkan anchor/scroll, tab baru, atau klik CTRL+Click
                if (!href || href.startsWith('#') || href.startsWith('javascript') || target === '_blank' || e.ctrlKey || e.metaKey) return;
                
                // Pastikan link masih berada di dalam web internal yang sama
                if (link.host !== window.location.host) return;

                e.preventDefault();
                pageTransition.classList.remove('loaded'); 
                
                // Pindah halaman jika animasi pemutih layar sudah tuntas
                setTimeout(() => {
                    window.location.href = href;
                }, 350); 
            });
        });
    })();
    </script>

    {{-- PWA Service Worker --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('[PWA] Service Worker registered:', reg.scope))
                    .catch(err => console.warn('[PWA] SW registration failed:', err));
            });
        }
    </script>

    {{-- ═══ AI CHAT WIDGET ═══ --}}
    <!-- Floating Button -->
    <button id="ai-chat-btn" aria-label="Buka Chat AI" class="fixed bottom-6 right-6 w-16 h-16 bg-gradient-to-tr from-rose-500 via-pink-600 to-indigo-600 text-white rounded-2xl shadow-[0_15px_35px_rgba(204,44,107,0.4)] flex items-center justify-center hover:scale-110 hover:-translate-y-1 hover:rotate-3 active:scale-95 transition-all z-50 overflow-hidden group border border-white/30">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(255,255,255,0.4),transparent_70%)] opacity-50"></div>
        <svg class="w-7 h-7 relative z-10 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
        </svg>
        <!-- Pulse Effect -->
        <span class="absolute inline-flex h-full w-full rounded-full bg-pink-400 opacity-20 animate-ping group-hover:hidden"></span>
    </button>

    <!-- Chat Modal Window -->
    <div id="ai-chat-window" class="fixed bottom-24 right-6 w-[calc(100vw-3rem)] max-w-[400px] h-[600px] max-h-[calc(100vh-8rem)] bg-white/95 backdrop-blur-xl rounded-[2rem] shadow-[0_20px_60px_rgba(0,0,0,0.15)] flex flex-col z-50 transform scale-0 translate-y-10 origin-bottom-right transition-all duration-500 cubic-bezier(0.34, 1.56, 0.64, 1) pointer-events-none opacity-0 border border-white/60 overflow-hidden">
        
        <!-- Header: Ultra Premium Glass -->
        <div class="px-6 py-5 bg-gradient-to-br from-rose-600/90 to-indigo-700/95 relative overflow-hidden flex items-center justify-between">
            <div class="absolute top-0 left-0 w-full h-full bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10 pointer-events-none"></div>
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full blur-3xl"></div>
            
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-11 h-11 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center shadow-lg border border-white/30 rotate-3 group-hover:rotate-0 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <h3 class="text-white font-extrabold text-base tracking-tight">SIPS AI Assistant</h3>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.8)] animate-pulse"></span>
                        <span class="text-white/80 text-[0.7rem] font-semibold uppercase tracking-widest leading-none">Intelligence Active</span>
                    </div>
                </div>
            </div>
            
            <button id="ai-chat-close" class="text-white/70 hover:text-white transition-all bg-white/10 p-2 rounded-xl hover:bg-rose-500/40 hover:scale-110 active:scale-90" aria-label="Tutup Chat">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Body / Chat Area -->
        <div id="ai-chat-body" class="flex-1 bg-gradient-to-b from-slate-50 to-white px-5 py-6 overflow-y-auto flex flex-col gap-5 custom-scrollbar" style="scroll-behavior: smooth;">
            <!-- Simple Date info -->
            <div class="flex justify-center mb-2">
                <span class="text-[0.6rem] font-bold text-slate-400 bg-slate-200/50 px-3 py-1 rounded-full uppercase tracking-tighter">Verified AI Support</span>
            </div>

            <!-- Bot Bubble -->
            <div class="flex items-start gap-3 max-w-[90%] group animate-fade-in-up">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-rose-500 to-indigo-600 flex items-center justify-center flex-shrink-0 mt-1 shadow-md text-white border border-white/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div class="bg-white px-5 py-4 rounded-[1.5rem] rounded-tl-none shadow-[0_5px_15px_rgba(0,0,0,0.03)] border border-slate-100 text-slate-700 leading-relaxed font-medium text-sm">
                    Halo! 👋 Saya asisten cerdas SIPS. <br><br>Ada yang bisa saya bantu terkait laporan atau informasi sekolah hari ini?
                </div>
            </div>
        </div>

        <!-- Footer / Input: Clean & Modern -->
        <div class="p-5 bg-white border-t border-slate-100 flex flex-col gap-3">
            <form id="ai-chat-form" class="relative flex items-center group">
                <input type="text" id="ai-chat-input" placeholder="Ketik pertanyaan Anda..." class="w-full pl-5 pr-14 py-4 bg-slate-100/80 border-2 border-transparent outline-none rounded-2xl text-sm font-semibold text-slate-700 focus:ring-0 focus:border-indigo-500/30 focus:bg-white transition-all shadow-inner placeholder:text-slate-400" autocomplete="off" required>
                <button type="submit" aria-label="Kirim Pesan" class="absolute right-2 w-11 h-11 flex items-center justify-center bg-indigo-600 text-white rounded-xl hover:bg-rose-600 hover:scale-105 active:scale-95 transition-all shadow-lg hover:shadow-rose-500/30 group-focus-within:bg-indigo-700">
                    <svg class="w-5 h-5 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </form>
            <p class="text-[0.6rem] text-center text-slate-400 font-medium tracking-wide italic">AI can make mistakes. Verify important info.</p>
        </div>
    </div>

    <!-- Script for UI interaction -->
    <script>
    (function() {
        const chatBtn = document.getElementById('ai-chat-btn');
        const chatWindow = document.getElementById('ai-chat-window');
        const chatClose = document.getElementById('ai-chat-close');
        const chatForm = document.getElementById('ai-chat-form');
        const chatInput = document.getElementById('ai-chat-input');
        const chatBody = document.getElementById('ai-chat-body');

        if (!chatBtn || !chatWindow) return;

        // Toggle logic
        function toggleChat() {
            const isClosed = chatWindow.classList.contains('scale-0');
            if (isClosed) {
                chatWindow.classList.remove('scale-0', 'pointer-events-none', 'opacity-0', 'translate-y-10');
                chatWindow.classList.add('scale-100', 'pointer-events-auto', 'opacity-100', 'translate-y-0');
                setTimeout(() => chatInput.focus(), 400);
            } else {
                chatWindow.classList.add('scale-0', 'pointer-events-none', 'opacity-0', 'translate-y-10');
                chatWindow.classList.remove('scale-100', 'pointer-events-auto', 'opacity-100', 'translate-y-0');
            }
        }

        chatBtn.addEventListener('click', toggleChat);
        chatClose.addEventListener('click', toggleChat);

        // Submitting message
        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const message = chatInput.value.trim();
            if (!message) return;

            addMessage(message, 'user');
            chatInput.value = '';

            // Show Typing state
            showTyping();

            try {
                const response = await fetch('/ai/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();
                removeTyping();

                if (data.status === 'success') {
                    addMessage(data.reply, 'bot');
                } else {
                    addMessage(data.message || 'Waduh, jatah chat AI gratisan sedang penuh. Coba lagi dalam 30 detik ya!', 'bot');
                }
            } catch (error) {
                removeTyping();
                addMessage('Koneksi terputus. Pastikan internet Anda stabil atau coba lagi nanti.', 'bot');
            }
        });

        // Add message bubble
        function addMessage(text, sender) {
            const wrap = document.createElement('div');
            
            if (sender === 'user') {
                wrap.className = 'flex items-start justify-end gap-3 max-w-[90%] self-end animate-fade-in-up';
                wrap.innerHTML = `
                    <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 text-white px-5 py-4 rounded-[1.5rem] rounded-tr-none shadow-lg shadow-indigo-200/50 font-medium leading-relaxed text-sm">
                        ${escapeHtml(text)}
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-slate-200 flex items-center justify-center flex-shrink-0 mt-1 overflow-hidden shadow-inner">
                        <img src="https://ui-avatars.com/api/?name=Guest&background=e2e8f0&color=475569" class="w-full h-full object-cover">
                    </div>
                `;
            } else {
                wrap.className = 'flex items-start gap-3 max-w-[90%] animate-fade-in-up';
                wrap.innerHTML = `
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-rose-500 to-indigo-600 flex items-center justify-center flex-shrink-0 mt-1 shadow-md text-white border border-white/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div class="bg-white px-5 py-4 rounded-[1.5rem] rounded-tl-none shadow-[0_5px_15px_rgba(0,0,0,0.03)] border border-slate-100 text-slate-700 leading-relaxed font-medium text-sm">
                        ${escapeHtml(text)}
                    </div>
                `;
            }
            
            chatBody.appendChild(wrap);
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        function showTyping() {
            const wrap = document.createElement('div');
            wrap.id = 'bot-typing';
            wrap.className = 'flex items-start gap-3 max-w-[90%] animate-fade-in-up';
            wrap.innerHTML = `
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-rose-500 to-indigo-600 flex items-center justify-center flex-shrink-0 mt-1 shadow-md text-white border border-white/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div class="bg-white/80 backdrop-blur-md px-5 py-4 rounded-[1.5rem] rounded-tl-none border border-slate-100 flex gap-1.5 items-center shadow-sm">
                    <div class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce"></div>
                    <div class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                    <div class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                </div>
            `;
            chatBody.appendChild(wrap);
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        function removeTyping() {
            const typing = document.getElementById('bot-typing');
            if (typing) typing.remove();
        }

        function escapeHtml(unsafe) {
            return unsafe
                 .replace(/&/g, "&amp;")
                 .replace(/</g, "&lt;")
                 .replace(/>/g, "&gt;")
                 .replace(/"/g, "&quot;")
                 .replace(/'/g, "&#039;");
        }
    })();
    </script>
    <style>
        .animate-fade-in-up {
            animation: fadeInUp 0.4s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(15, 23, 42, 0.05);
            border-radius: 20px;
        }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb {
            background: rgba(15, 23, 42, 0.1);
        }
    </style>
</body>
</html>
