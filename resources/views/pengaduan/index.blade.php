@extends('layout.dashboard')

@section('title', 'Daftar Pengaduan - SIPS - Sistem Informasi Pengaduan Sekolah')

@section('content')
<div class="pi-wrapper">
    {{-- Header --}}
    <div class="pi-header">
        <div>
            <h1 class="pi-title">Daftar Pengaduan</h1>
            <p class="pi-subtitle">Kelola dan pantau seluruh pengaduan</p>
        </div>
        <div class="pi-actions">
            <a href="{{ route('pengaduan.export') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" class="pi-btn-export">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export
            </a>
            <a href="{{ route('pengaduan.create') }}" class="pi-btn-create">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Pengaduan
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="pi-alert pi-alert-success">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="pi-alert pi-alert-error">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Stats Mini --}}
    @if (auth()->user()->role == 'admin')
    <div class="pi-stats-grid">
        @php
        $statItems = [
            ['label' => 'Total', 'value' => $stats['total'], 'color' => 'text-gray-700', 'bg' => 'bg-gray-50', 'param' => ''],
            ['label' => 'Pending', 'value' => $stats['pending'], 'color' => 'text-yellow-600', 'bg' => 'bg-yellow-50', 'param' => 'pending'],
            ['label' => 'Verified', 'value' => $stats['verified'], 'color' => 'text-blue-600', 'bg' => 'bg-blue-50', 'param' => 'verified'],
            ['label' => 'Diproses', 'value' => $stats['in_progress'], 'color' => 'text-purple-600', 'bg' => 'bg-purple-50', 'param' => 'in_progress'],
            ['label' => 'Selesai', 'value' => $stats['resolved'], 'color' => 'text-green-600', 'bg' => 'bg-green-50', 'param' => 'resolved'],
            ['label' => 'Ditolak', 'value' => $stats['rejected'], 'color' => 'text-red-600', 'bg' => 'bg-red-50', 'param' => 'rejected'],
        ];
        @endphp
        @foreach($statItems as $s)
        <a href="{{ route('pengaduan.index', array_merge(request()->except(['status','page']), $s['param'] ? ['status' => $s['param']] : [])) }}"
           class="pi-stat-card {{ $s['bg'] }} {{ request('status') == $s['param'] && $s['param'] ? 'active' : '' }}">
            <p class="pi-stat-value {{ $s['color'] }}">{{ $s['value'] }}</p>
            <p class="pi-stat-label">{{ $s['label'] }}</p>
        </a>
        @endforeach
    </div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('pengaduan.index') }}" class="pi-filters">
        <div class="pi-filters-grid">
            <div>
                <div class="pi-search-box">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pengaduan..." class="pi-input">
                </div>
            </div>
            <select name="status" class="pi-input">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Diproses</option>
                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Selesai</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
            <select name="urgensi" class="pi-input">
                <option value="">Semua Urgensi</option>
                <option value="rendah" {{ request('urgensi') == 'rendah' ? 'selected' : '' }}>Rendah</option>
                <option value="sedang" {{ request('urgensi') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                <option value="tinggi" {{ request('urgensi') == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                <option value="darurat" {{ request('urgensi') == 'darurat' ? 'selected' : '' }}>Darurat</option>
            </select>
            <div class="pi-filter-actions">
                <button type="submit" class="pi-btn-submit">Filter</button>
                <a href="{{ route('pengaduan.index') }}" class="pi-btn-reset">Reset</a>
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="pi-table-container">
        <div class="pi-table-wrapper">
            <table class="pi-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Judul</th>
                        <th class="hidden md:table-cell">Kategori</th>
                        <th class="hidden lg:table-cell">Urgensi</th>
                        <th>Status</th>
                        <th class="hidden lg:table-cell">Tanggal</th>
                        @if (auth()->user()->role == 'admin')
                        <th style="text-align: right;">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengaduan as $p)
                    <tr>
                        <td>
                            <span class="pi-badge-kode">{{ $p->kode_unik }}</span>
                        </td>
                        <td>
                            <div>
                                <a href="{{ route('pengaduan.show', $p->kode_unik) }}" class="pi-row-title">{{ $p->judul }}</a>
                                @if($p->is_overdue_sla)
                                <span class="pi-sla-warn">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Waktu penanganan terlampaui
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="hidden md:table-cell">
                            <span class="text-gray-600 dark:text-gray-400">{{ $p->kategori->nama ?? '-' }}</span>
                        </td>
                        <td class="hidden lg:table-cell">
                            <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-semibold {{ $p->urgensi_badge['class'] }}">
                                {{ $p->urgensi_badge['text'] }}
                            </span>
                        </td>
                        <td>
                            <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-semibold {{ $p->status_badge['class'] }}">
                                {{ $p->status_badge['text'] }}
                            </span>
                        </td>
                        <td class="hidden lg:table-cell" style="color: #6b7280; font-size: 0.75rem;">
                            {{ $p->created_at->format('d M Y') }}
                        </td>
                        @if (auth()->user()->role == 'admin')
                        <td style="text-align: right;">
                            <div class="pi-table-actions">
                                <a href="{{ route('pengaduan.show', $p->kode_unik) }}" class="pi-action-btn view" title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                @can('update', $p)
                                <a href="{{ route('pengaduan.edit', $p) }}" class="pi-action-btn edit" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                @endcan
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="pi-empty">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="pi-empty-title">Tidak ada pengaduan ditemukan</p>
                                <p class="pi-empty-sub">Coba ubah filter pencarian</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination --}}
        @if($pengaduan->hasPages())
        <div class="pi-pagination">
            {{ $pengaduan->appends(request()->except('page'))->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
