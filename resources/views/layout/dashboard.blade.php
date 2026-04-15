<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIPS - Sistem Informasi Pengaduan Sekolah')</title>

    {{-- PWA Meta Tags --}}
    <meta name="theme-color" content="#cc2c6b">
    <meta name="description" content="SIPS - Sistem Informasi Pengaduan Sekolah yang mudah, cepat, dan transparan.">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SIPS">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192x192.png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/pengaduan.css') }}">
    @stack('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');
        * { font-family: 'Poppins', sans-serif; box-sizing: border-box; }

        svg { display: inline-block; vertical-align: middle; }
        svg:not([width]):not([height]):not([class*="w-"]) { width: 1.25rem; height: 1.25rem; }

        .sidebar { display: none; }
        .sidebar-overlay { display: none; }

        .main-content { min-height: 100vh; background: #f8fafc; margin-left: 0 !important; padding-left: 0 !important; }

        .topbar { background: white; border-bottom: 1px solid #f1f5f9; position: sticky; top: 0; z-index: 30; }

        .notif-badge { background: #cc2c6b; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 10px; font-weight: 700; display: flex; align-items: center; justify-content: center; position: absolute; top: -4px; right: -4px; }

        /* =============================================
           DROPDOWN BASE — Glassmorphism + Animasi Buka/Tutup
           Pakai clip-path supaya animasi collapse ke atas saat tutup
           ============================================= */
        .user-dropdown,
        .notif-dropdown {
            position: absolute;
            top: calc(100% + 12px);
            background: rgba(28, 28, 30, 0.82);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 18px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.4);
            border: 1px solid rgba(255,255,255,0.1);
            z-index: 100;
            overflow: hidden;
            max-height: 85vh;
            overflow-y: auto;
            overflow-x: hidden;

            /* State awal: tersembunyi, clip dari atas ke bawah */
            clip-path: inset(0 0 100% 0 round 18px);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px);
            pointer-events: none;
            transition:
                clip-path   0.38s cubic-bezier(0.22, 1, 0.36, 1),
                opacity     0.25s ease,
                transform   0.38s cubic-bezier(0.22, 1, 0.36, 1),
                visibility  0s   linear 0.38s;
        }
        .user-dropdown  { left: 0;  width: 280px; }
        .notif-dropdown { right: 0; width: 320px; }

        /* State terbuka: expand dari atas ke bawah */
        .user-dropdown.open,
        .notif-dropdown.open {
            clip-path: inset(0 0 0% 0 round 18px);
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
            pointer-events: auto;
            transition:
                clip-path   0.38s cubic-bezier(0.22, 1, 0.36, 1),
                opacity     0.25s ease,
                transform   0.38s cubic-bezier(0.22, 1, 0.36, 1),
                visibility  0s   linear 0s;
        }

        /* =============================================
           DROPDOWN ITEMS — Slide kiri ke kanan (stagger)
           Pakai CSS custom property --item-index yang di-set via JS
           HANYA berlaku untuk #user-dropdown supaya tidak bocor ke notif
           ============================================= */
        #user-dropdown .user-dd-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 18px;
            color: #e5e5e5;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            border: none;
            width: 100%;
            text-align: left;
            background: transparent;

            /* State awal: tersembunyi, geser ke kiri */
            opacity: 0;
            transform: translateX(-10px);
            transition:
                opacity   0.22s ease,
                transform 0.22s cubic-bezier(0.22, 1, 0.36, 1),
                background 0.2s;
        }

        /* Saat open: muncul dari kiri ke kanan dengan stagger via --item-index */
        #user-dropdown.open .user-dd-item {
            opacity: 1;
            transform: translateX(0);
            transition-delay: calc(var(--item-index, 0) * 0.04s + 0.05s);
        }

        /* Saat tutup: semua item langsung hilang, tanpa delay */
        #user-dropdown:not(.open) .user-dd-item {
            transition:
                opacity    0.1s ease,
                transform  0.1s ease,
                background 0.2s;
            transition-delay: 0s !important;
        }

        /* Hover item */
        .user-dd-item:hover { background: rgba(255, 255, 255, 0.12); color: white; }
        .user-dd-item svg { width: 1.25rem; height: 1.25rem; color: #a3a3a3; stroke-width: 2; transition: color 0.2s; }
        .user-dd-item:hover svg { color: white; }
        .user-dd-separator { height: 1px; background: rgba(255,255,255,0.08); margin: 6px 0; }

        /* Dark mode */
        html.dark .user-dropdown,
        html.dark .notif-dropdown {
            background: rgba(20, 20, 22, 0.88) !important;
            border-color: rgba(255,255,255,0.05) !important;
        }

        /* =============================================
           DARK MODE TOGGLE SWITCH
           ============================================= */
        .toggle-switch { position: relative; width: 36px; height: 20px; }
        .switch-label { display: block; overflow: hidden; cursor: pointer; border-radius: 20px; background-color: rgba(255,255,255,0.2); height: 100%; transition: background-color 0.2s; margin: 0; }
        .switch-label::before { content: ""; display: block; width: 14px; height: 14px; margin: 3px; background: white; border-radius: 50%; transition: transform 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.3); }
        #darkModeToggle:checked + .switch-label { background-color: #e5007ead; }
        #darkModeToggle:checked + .switch-label::before { transform: translateX(16px); }

        /* =============================================
           SCROLLBAR
           ============================================= */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 2px; }

        /* =============================================
           GLOBAL DARK MODE OVERRIDES
           ============================================= */
        html.dark .main-content { background-color: #121212 !important; margin-left: 0 !important; }
        html.dark .topbar { background-color: #1e1e1e !important; border-bottom-color: #333 !important; }
        html.dark .bg-white { background-color: #1e1e1e !important; border-color: #333 !important; color: #e5e5e5 !important; }
        html.dark .bg-gray-50 { background-color: #121212 !important; border-color: #2a2a2a !important; }
        html.dark .bg-gray-100 { background-color: #2a2a2a !important; }

        html.dark .bg-red-50    { background-color: #450a0a !important; border-color: #7f1d1d !important; }
        html.dark .bg-yellow-50 { background-color: #422006 !important; border-color: #78350f !important; }
        html.dark .bg-blue-50   { background-color: #172554 !important; border-color: #1e3a8a !important; }
        html.dark .bg-purple-50 { background-color: #3b0764 !important; border-color: #581c87 !important; }
        html.dark .bg-green-50  { background-color: #052e16 !important; border-color: #14532d !important; }
        html.dark .bg-orange-50 { background-color: #431407 !important; border-color: #7c2d12 !important; }
        html.dark .bg-pink-50   { background-color: #500724 !important; border-color: #831843 !important; }

        html.dark .bg-red-100    { background-color: #7f1d1d !important; }
        html.dark .bg-yellow-100 { background-color: #78350f !important; }
        html.dark .bg-blue-100   { background-color: #1e3a8a !important; }
        html.dark .bg-purple-100 { background-color: #581c87 !important; }
        html.dark .bg-green-100  { background-color: #14532d !important; }
        html.dark .bg-orange-100 { background-color: #7c2d12 !important; }
        html.dark .bg-pink-100   { background-color: #831843 !important; }

        html.dark .hover\:bg-gray-100:hover { background-color: #262626 !important; }
        html.dark .hover\:bg-gray-50:hover  { background-color: #1a1a1a !important; }

        html.dark .text-gray-900,
        html.dark .text-gray-800,
        html.dark .text-gray-700,
        html.dark .text-gray-600 { color: #f5f5f5 !important; }
        html.dark .text-gray-500,
        html.dark .text-gray-400 { color: #cbd5e1 !important; }

        html.dark .border-gray-100,
        html.dark .border-gray-200 { border-color: #404040 !important; }
        html.dark .shadow-sm,
        html.dark .shadow-md,
        html.dark .shadow-lg { box-shadow: 0 4px 20px rgba(0,0,0,0.5) !important; }

        html.dark td, html.dark th { border-color: #404040 !important; }
        html.dark input, html.dark textarea, html.dark select {
            background-color: #1e1e1e !important;
            color: #f5f5f5 !important;
            border-color: #404040 !important;
        }

        html.dark .text-green-600  { color: #4ade80 !important; }
        html.dark .text-blue-600   { color: #60a5fa !important; }
        html.dark .text-red-600    { color: #f87171 !important; }
        html.dark .text-yellow-600 { color: #fbbf24 !important; }
        html.dark .text-orange-600 { color: #fb923c !important; }
        html.dark .text-pink-600   { color: #f472b6 !important; }
        html.dark .text-purple-600 { color: #a78bfa !important; }
        html.dark .text-gray-600   { color: #cbd5e1 !important; }
        html.dark .text-gray-700   { color: #e2e8f0 !important; }
        html.dark .text-gray-900   { color: #ffffff !important; }

        html.dark .text-yellow-700, html.dark .text-yellow-800 { color: #fef08a !important; }
        html.dark .text-blue-700,   html.dark .text-blue-800   { color: #bfdbfe !important; }
        html.dark .text-purple-700, html.dark .text-purple-800 { color: #e9d5ff !important; }
        html.dark .text-green-700,  html.dark .text-green-800  { color: #bbf7d0 !important; }
        html.dark .text-red-700,    html.dark .text-red-800    { color: #fecaca !important; }
        html.dark .text-orange-700, html.dark .text-orange-800 { color: #fed7aa !important; }

        /* =============================================
           THEME TRANSITION — Smooth fade dark/light
           ============================================= */
        html.switching-theme,
        html.switching-theme * {
            transition: background-color 450ms cubic-bezier(0.4, 0, 0.2, 1),
                        color            450ms cubic-bezier(0.4, 0, 0.2, 1),
                        border-color     450ms cubic-bezier(0.4, 0, 0.2, 1),
                        box-shadow       450ms cubic-bezier(0.4, 0, 0.2, 1),
                        fill             450ms cubic-bezier(0.4, 0, 0.2, 1) !important;
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
            transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.4s;
        }
        #page-transition.loaded {
            opacity: 0;
        }
        html.dark #page-transition {
            background-color: #121212;
        }
    </style>
</head>
<body class="main-content h-full bg-slate-50">

    <!-- Page Transition Overlay -->
    <div id="page-transition"></div>

    <!-- Topbar -->
    <header class="topbar px-6 py-3 flex items-center justify-between gap-4">

        <!-- Left: Burger Menu -->
        <div class="flex items-center gap-4">
            <div class="relative" id="user-menu-wrapper">

                <button onclick="toggleUserMenu()" class="flex items-center justify-center w-10 h-10 rounded-xl hover:bg-gray-100 transition-colors text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <div class="user-dropdown" id="user-dropdown">

                    <!-- Profile Card -->
                    <div style="padding: 16px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; gap: 12px;">
                        <img src="{{ auth()->user()?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()?->name ?? 'U') . '&background=cc2c6b&color=fff' }}"
                             alt="{{ auth()->user()?->name }}"
                             style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover; flex-shrink: 0;">
                        <div style="min-width: 0;">
                            <p style="font-size: 0.875rem; font-weight: 700; color: #f5f5f5; margin: 0;">{{ auth()->user()?->name }}</p>
                            <p style="font-size: 0.65rem; font-weight: 600; color: #a3a3a3; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">{{ auth()->user()?->role ?? 'user' }}</p>
                        </div>
                    </div>

                    <!-- Multi-account -->
                    <div style="padding: 4px 0; border-bottom: 1px solid rgba(255,255,255,0.08); background: rgba(0,0,0,0.05);">
                        @php
                            $multiAccounts = session('multi_accounts', []);
                            $otherAccountIds = array_diff($multiAccounts, [auth()->id()]);
                        @endphp

                        @if(count($otherAccountIds) > 0)
                            @php $otherUsers = \App\Models\User::whereIn('id', $otherAccountIds)->get(); @endphp
                            @foreach($otherUsers as $ou)
                            <a href="{{ route('switch.account', $ou->id) }}" class="user-dd-item" style="margin: 4px 8px; border-radius: 8px; justify-content: space-between;">
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <img src="{{ $ou->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($ou->name ?? 'U') . '&background=cc2c6b&color=fff' }}"
                                         alt="" style="width:24px; height:24px; border-radius:50%;">
                                    <div style="display:flex; flex-direction:column; min-width:0;">
                                        <span style="font-size:0.75rem; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; line-height:1.2; color:#f5f5f5;">{{ $ou->name }}</span>
                                        <span style="font-size:0.6rem; color:#a3a3a3;">Ganti Akun</span>
                                    </div>
                                </div>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4 text-gray-400"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            </a>
                            @endforeach
                        @endif

                        <a href="{{ route('login', ['add_account' => 1]) }}" class="user-dd-item" style="margin: 4px 8px; border-radius: 8px;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            Tambahkan Akun
                        </a>
                    </div>

                    <!-- Nav Label -->
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px 6px;">
                        <p style="font-size: 0.75rem; font-weight: 700; color: #a3a3a3; text-transform: uppercase;">Menu Navigasi</p>
                    </div>

                    <a href="{{ route('dashboard') }}" class="user-dd-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h3.586a1 1 0 01.707.293l2.414 2.414A1 1 0 0012.414 8H19a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
                        Dashboard
                    </a>

                    <a href="{{ route('pengaduan.index') }}" class="user-dd-item" style="justify-content: space-between">
                        <div style="display:flex; align-items:center; gap:14px;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Seluruh Pengaduan
                        </div>
                        @php $pendingCount = auth()->user() ? \App\Models\Pengaduan::where('status', 'pending')->count() : 0; @endphp
                        @if($pendingCount > 0 && auth()->user()->role == 'admin')
                        <span style="font-size: 0.65rem; font-weight: bold; background: #cc2c6b; color: white; padding: 2px 6px; border-radius: 12px;">{{ $pendingCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('pengaduan.create') }}" class="user-dd-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Buat Laporan
                    </a>

                    <a href="{{ route('track.index') }}" class="user-dd-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Lacak Laporan
                    </a>

                    <a href="{{ route('panduan') }}" class="user-dd-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Panduan Melapor
                    </a>

                    @if(auth()->check() && auth()->user()->isPetugas())
                    <div class="user-dd-separator"></div>
                    <div style="padding: 10px 16px 4px;"><p style="font-size: 0.65rem; font-weight: 700; color: #a3a3a3; text-transform: uppercase;">Analitik Laporan</p></div>

                    <a href="{{ route('dashboard.analytics') }}" class="user-dd-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Bagan & Statistik
                    </a>

                    <a href="{{ route('dashboard.monthly-report') }}" class="user-dd-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Rekap Bulanan
                    </a>
                    @endif

                    @if(auth()->check() && auth()->user()->isAdmin())
                    <div class="user-dd-separator"></div>
                    <div style="padding: 10px 16px 4px;"><p style="font-size: 0.65rem; font-weight: 700; color: #a3a3a3; text-transform: uppercase;">Administrasi Web</p></div>

                    <a href="{{ route('kategori.index') }}" class="user-dd-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        Kategori (Topik)
                    </a>

                    <a href="{{ route('users.index') }}" class="user-dd-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Manajemen Akun
                    </a>

                    <a href="{{ route('departments.index') }}" class="user-dd-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Manajemen Departemen
                    </a>

                    <a href="{{ route('admin.logs.index') }}" class="user-dd-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Log Aktivitas Admin
                    </a>

                    <a href="{{ route('settings.home') }}" class="user-dd-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Pengaturan Front-end
                    </a>
                    @endif

                    <div class="user-dd-separator"></div>
                    <div style="padding: 10px 16px 4px;"><p style="font-size: 0.65rem; font-weight: 700; color: #a3a3a3; text-transform: uppercase;">Pengaturan Profil</p></div>

                    <a href="{{ route('profile.edit') }}" class="user-dd-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Profil Saya
                    </a>

                    <a href="{{ route('profile.activity') }}" class="user-dd-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Riwayat Aktivitas
                    </a>

                    <div class="user-dd-item" style="justify-content: space-between;" onclick="toggleDarkMod(event)">
                        <div style="display:flex; align-items:center; gap:14px;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                            Mode Layar Gelap
                        </div>
                        <div class="toggle-switch">
                            <input type="checkbox" id="darkModeToggle" style="display:none;" onchange="handleDarkToggle(event)">
                            <label for="darkModeToggle" class="switch-label"></label>
                        </div>
                    </div>

                    <div class="user-dd-separator"></div>

                    <a href="{{ route('home') }}" class="user-dd-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        Kembali Ke Beranda
                    </a>

                    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="user-dd-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Keluar Akun
                        </button>
                    </form>

                </div><!-- /#user-dropdown -->
            </div>
        </div>

        <!-- Right: Breadcrumb + Notif -->
        <div class="flex items-center gap-4">

            <div class="hidden md:block text-right">
                <h2 class="text-sm font-bold text-gray-900">@yield('title', 'Dashboard')</h2>
                <div class="flex items-center justify-end gap-1 text-[11px] font-medium text-gray-400">
                    <a href="{{ route('dashboard') }}" class="hover:text-pink-600 transition-colors">Dashboard</a>
                    @hasSection('breadcrumb')
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    @yield('breadcrumb')
                    @endif
                </div>
            </div>

            <div class="h-8 w-px bg-gray-200 hidden md:block"></div>

            <!-- Notif Dropdown -->
            @php
                $activities = auth()->user()?->activityLogs()->latest()->limit(5)->get() ?? collect();
            @endphp
            <div class="relative" id="notif-menu-wrapper">
                <button onclick="toggleNotifMenu()" class="relative p-2.5 rounded-full bg-gray-50 text-gray-500 hover:text-pink-600 hover:bg-pink-50 transition-colors border border-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </button>

                <div class="notif-dropdown" id="notif-dropdown">
                    <div style="padding: 14px 18px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-weight: 700; font-size: 0.85rem; color: #f5f5f5;">Aktivitas Terbaru</span>
                    </div>

                    <div style="max-height: 320px; overflow-y: auto;">
                        @forelse($activities as $a)
                        <div class="flex flex-col gap-1 p-4 hover:bg-white/10 transition-colors" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-pink-500"></div>
                                    <span style="font-weight: 700; font-size: 0.75rem; color: #f5f5f5;">{{ ucfirst($a->action) }}</span>
                                </div>
                                <span style="font-size: 0.6rem; color: #a3a3a3;">{{ $a->created_at->diffForHumans() }}</span>
                            </div>
                            <p style="font-size: 0.7rem; color: #cbd5e1; line-height: 1.4;">{{ $a->target }} - {{ $a->details }}</p>
                        </div>
                        @empty
                        <div class="p-8 text-center">
                            <svg class="w-10 h-10 mx-auto mb-3" style="color: rgba(255,255,255,0.3);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <p class="text-xs" style="color: rgba(255,255,255,0.5);">Belum ada aktivitas</p>
                        </div>
                        @endforelse
                    </div>

                    <div style="padding: 12px; text-align: center; border-top: 1px solid rgba(255,255,255,0.1);">
                        <a href="{{ route('profile.activity') }}" style="font-size: 0.7rem; font-weight: 600; color: #b8a9b0; text-decoration: none;">Lihat Log Selengkapnya</a>
                    </div>
                </div>
            </div><!-- /#notif-menu-wrapper -->

        </div>
    </header>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="mx-6 mt-4 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3 text-sm text-green-700" id="flash-success">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
        <button onclick="this.parentElement.remove()" class="ml-auto text-green-400 hover:text-green-600">×</button>
    </div>
    @endif
    @if(session('error'))
    <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3 text-sm text-red-700" id="flash-error">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
        <button onclick="this.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600">×</button>
    </div>
    @endif

    <!-- Page Content -->
    <div class="px-6">
        @yield('content')
    </div>

    <script>
    // ── Theme Init ──────────────────────────────────────────────
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
        setTimeout(() => {
            const t = document.getElementById('darkModeToggle');
            if (t) t.checked = true;
        }, 100);
    }

    function handleDarkToggle(e) {
        // Tambahkan class transisi ke root html
        document.documentElement.classList.add('switching-theme');

        if (e.target.checked) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }

        // Hapus class transisi setelah durasi selesai (450ms + buffer)
        setTimeout(() => {
            document.documentElement.classList.remove('switching-theme');
        }, 500);
    }

    function toggleDarkMod(e) {
        e.stopPropagation();
        const t = document.getElementById('darkModeToggle');
        if (e.target !== t && !e.target.classList.contains('switch-label')) {
            t.checked = !t.checked;
            handleDarkToggle({ target: t });
        }
    }

    // ── Stagger Index — set --item-index ke tiap .user-dd-item ──
    // Dijalankan sekali saat DOM ready supaya CSS transition-delay akurat
    (function assignItemIndex() {
        const items = document.querySelectorAll('#user-dropdown .user-dd-item');
        items.forEach((el, i) => el.style.setProperty('--item-index', i));
    })();

    // ── Dropdown Toggles ────────────────────────────────────────
    function toggleUserMenu() {
        const dd    = document.getElementById('user-dropdown');
        const notif = document.getElementById('notif-dropdown');
        notif.classList.remove('open');
        dd.classList.toggle('open');
    }

    function toggleNotifMenu() {
        const dd    = document.getElementById('user-dropdown');
        const notif = document.getElementById('notif-dropdown');
        dd.classList.remove('open');
        notif.classList.toggle('open');
    }

    // Tutup saat klik di luar
    document.addEventListener('click', function(e) {
        if (!document.getElementById('user-menu-wrapper').contains(e.target)) {
            document.getElementById('user-dropdown').classList.remove('open');
        }
        if (!document.getElementById('notif-menu-wrapper').contains(e.target)) {
            document.getElementById('notif-dropdown').classList.remove('open');
        }
    });

    // ── Auto dismiss flash ───────────────────────────────────────
    setTimeout(() => {
        ['flash-success', 'flash-error'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.style.transition = 'opacity 0.5s';
                el.style.opacity    = '0';
                setTimeout(() => el.remove(), 500);
            }
        });
    }, 4000);

    // ── Transisi Halaman (Page Transition) ──
    const pageTransition = document.getElementById('page-transition');
    window.addEventListener('load', function() {
        setTimeout(() => pageTransition.classList.add('loaded'), 50);
    });
    
    document.querySelectorAll('a').forEach(function(link) {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            const target = this.getAttribute('target');
            
            if (!href || href.startsWith('#') || href.startsWith('javascript') || target === '_blank' || e.ctrlKey || e.metaKey) return;
            if (link.host !== window.location.host) return;

            e.preventDefault();
            pageTransition.classList.remove('loaded');
            
            setTimeout(() => {
                window.location.href = href;
            }, 350); 
        });
    });
    </script>

    @stack('scripts')

    <!-- PWA Service Worker -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('[PWA] SW registered:', reg.scope))
                    .catch(err => console.warn('[PWA] SW failed:', err));
            });
        }
    </script>
</body>
</html>

