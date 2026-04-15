@extends('layout.dashboard')

@section('title', 'Dashboard - SIPS - Sarana Informasi Pengaduan Sekolah')

@section('content')
<div class="py-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">Selamat datang, <span class="font-semibold text-pink-600">{{ auth()->user()->name }}</span></p>
        </div>
        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                <select name="period" onchange="this.form.submit()" class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-300 bg-white">
                    <option value="7" {{ request('period') == '7' ? 'selected' : '' }}>7 Hari Terakhir</option>
                    <option value="30" {{ request('period', '30') == '30' ? 'selected' : '' }}>30 Hari Terakhir</option>
                    <option value="90" {{ request('period') == '90' ? 'selected' : '' }}>90 Hari Terakhir</option>
                </select>
            </form>
            @if(auth()->user()->isAdmin() || auth()->user()->isPetugas())
            <a href="{{ route('pengaduan.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-white text-sm font-bold rounded-xl transition-all hover:-translate-y-0.5 hover:shadow-lg" style="background: linear-gradient(135deg, #cc2c6b, #374151);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Pengaduan
            </a>
            @endif
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Stats Cards --}}
    @if(auth()->user()->isAdmin() || auth()->user()->isPetugas())
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        {{-- Total --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #cc2c6b20, #cc2c6b10);">
                    <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total</span>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
            <p class="text-sm text-gray-500 mt-1">Total Pengaduan</p>
        </div>

        {{-- Pending --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-yellow-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-semibold text-yellow-500 uppercase tracking-wide">Pending</span>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['pending']) }}</p>
            <p class="text-sm text-gray-500 mt-1">Menunggu Verifikasi</p>
        </div>

        {{-- In Progress --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <span class="text-xs font-semibold text-purple-500 uppercase tracking-wide">Diproses</span>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['in_progress']) }}</p>
            <p class="text-sm text-gray-500 mt-1">Sedang Ditangani</p>
        </div>

        {{-- Resolved --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-semibold text-green-500 uppercase tracking-wide">Selesai</span>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['resolved']) }}</p>
            <p class="text-sm text-gray-500 mt-1">Terselesaikan</p>
        </div>
    </div>

    {{-- Additional stats row --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-gradient-to-br from-pink-500 to-rose-600 rounded-2xl p-5 text-white shadow-sm">
            <p class="text-3xl font-bold">{{ $stats['resolution_rate'] }}%</p>
            <p class="text-sm text-pink-100 mt-1">Tingkat Penyelesaian</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <p class="text-3xl font-bold text-gray-900">{{ $stats['avg_resolution_hours'] }}j</p>
            <p class="text-sm text-gray-500 mt-1">Rata-rata Penanganan</p>
        </div>
        <div class="bg-white rounded-2xl border border-red-100 p-5 shadow-sm border-l-4 border-l-red-400">
            <p class="text-3xl font-bold text-red-600">{{ $stats['urgent'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Pengaduan Darurat</p>
        </div>
        <div class="bg-white rounded-2xl border border-orange-100 p-5 shadow-sm border-l-4 border-l-orange-400">
            <p class="text-3xl font-bold text-orange-600">{{ $stats['overdue_sla'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Batas Waktu Terlampaui</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Trend Chart --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-base font-bold text-gray-900">Tren Pengaduan</h2>
                <div class="flex items-center gap-4 text-xs text-gray-500">
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-pink-500 inline-block"></span> Total</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span> Selesai</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-yellow-400 inline-block"></span> Pending</span>
                </div>
            </div>
            <canvas id="trendChart" height="120"></canvas>
        </div>

        {{-- Status Distribution --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h2 class="text-base font-bold text-gray-900 mb-6">Distribusi Status</h2>
            <canvas id="statusChart" height="180"></canvas>
        </div>
    </div>
    @else
    {{-- User Normal Stats --}}
    <div class="grid grid-cols-2 gap-4 mb-8">
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #cc2c6b20, #cc2c6b10);">
                    <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Laporan</span>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['my_pengaduan'] ?? 0) }}</p>
            <p class="text-sm text-gray-500 mt-1">Laporan Anda Secara Keseluruhan</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-semibold text-green-500 uppercase tracking-wide">Selesai</span>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['my_resolved'] ?? 0) }}</p>
            <p class="text-sm text-gray-500 mt-1">Laporan Berhasil Diselesaikan</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Pengaduan --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-900">Pengaduan Terbaru</h2>
                <a href="{{ route('pengaduan.index') }}" class="text-xs text-pink-600 font-semibold hover:underline">Lihat Semua →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentPengaduan as $p)
                <a href="{{ route('pengaduan.show', $p->kode_unik) }}" class="flex items-start gap-4 px-6 py-4 hover:bg-gray-50 transition-colors block">
                    <div class="flex-shrink-0 mt-0.5">
                        <span class="inline-block px-2 py-0.5 rounded-lg text-xs font-semibold {{ $p->status_badge['class'] }}">
                            {{ $p->status_badge['text'] }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $p->judul }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $p->kode_unik }} · {{ $p->kategori->nama ?? 'N/A' }} · {{ $p->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <span class="flex-shrink-0 px-2 py-0.5 rounded-lg text-xs font-semibold {{ $p->urgensi_badge['class'] }}">
                        {{ $p->urgensi_badge['text'] }}
                    </span>
                </a>
                @empty
                <div class="px-6 py-12 text-center">
                    <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p class="text-sm text-gray-400">Belum ada pengaduan</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Right Column --}}
        <div class="flex flex-col gap-6">
            {{-- Urgent Pengaduan --}}
            @if($urgentPengaduan->count() > 0)
            <div class="bg-red-50 border border-red-100 rounded-2xl shadow-sm overflow-hidden">
                <div class="flex items-center gap-2 px-5 py-4 border-b border-red-100">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <h2 class="text-sm font-bold text-red-700">Pengaduan Darurat</h2>
                </div>
                <div class="divide-y divide-red-100">
                    @foreach($urgentPengaduan as $p)
                    <a href="{{ route('pengaduan.show', $p->kode_unik) }}" class="block px-5 py-3 hover:bg-red-100 transition-colors">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $p->judul }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $p->kode_unik }} · {{ $p->created_at->diffForHumans() }}</p>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Top Kategori --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-900">Kategori Teratas</h2>
                </div>
                <div class="p-5 space-y-3">
                    @forelse($topKategoris as $k)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $k->warna ?? '#cc2c6b' }}"></span>
                            <span class="text-sm text-gray-700 truncate">{{ $k->nama }}</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900 ml-2">{{ $k->pengaduan_count }}</span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 text-center py-4">Belum ada data</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@if(auth()->user()->isAdmin() || auth()->user()->isPetugas())
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Trend Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: @json($trendData['labels']),
            datasets: [
                {
                    label: 'Total',
                    data: @json($trendData['total']),
                    borderColor: '#cc2c6b',
                    backgroundColor: 'rgba(204, 44, 107,0.08)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                },
                {
                    label: 'Selesai',
                    data: @json($trendData['resolved']),
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34,197,94,0.06)',
                    borderWidth: 2,
                    fill: false,
                    tension: 0.4,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                },
                {
                    label: 'Pending',
                    data: @json($trendData['pending']),
                    borderColor: '#eab308',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    fill: false,
                    tension: 0.4,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, tick: { font: { size: 11 } } },
                y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, precision: 0 } }
            }
        }
    });

    // Status Doughnut Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: @json($statusData['labels']),
            datasets: [{
                data: @json($statusData['values']),
                backgroundColor: ['#fbbf24', '#3b82f6', '#a855f7', '#22c55e', '#ef4444'],
                borderWidth: 0,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 11 }, padding: 12, usePointStyle: true, pointStyleWidth: 8 }
                }
            },
            cutout: '65%'
        }
    });
</script>
@endif
@endpush
@endsection

