@extends('layout.dashboard')

@section('title', 'Laporan Bulanan - SIPS - Sistem Informasi Pengaduan Sekolah')

@section('content')
<div class="py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Laporan Bulanan</h1>
            <p class="text-sm text-gray-500 mt-1">Rekap pengaduan per bulan</p>
        </div>
        <div class="flex items-center gap-3">
            <input type="month" id="month-picker" value="{{ date('Y-m') }}" max="{{ date('Y-m') }}" onchange="loadReport(this.value)"
                   class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-300 bg-white">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">← Dashboard</a>
        </div>
    </div>

    {{-- Report Period Header --}}
    <div class="bg-gradient-to-r from-pink-500 to-gray-700 rounded-2xl p-6 text-white mb-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-pink-200 text-sm font-medium">Periode Laporan</p>
                <h2 id="report-period" class="text-2xl font-bold mt-1">Memuat...</h2>
            </div>
            <svg class="w-12 h-12 text-pink-300 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <p id="rep-total" class="text-3xl font-bold text-gray-900">-</p>
            <p class="text-sm text-gray-500 mt-1">Total Masuk</p>
        </div>
        <div class="bg-white rounded-2xl border border-green-100 p-5 shadow-sm border-l-4 border-l-green-400">
            <p id="rep-resolved" class="text-3xl font-bold text-green-600">-</p>
            <p class="text-sm text-gray-500 mt-1">Terselesaikan</p>
        </div>
        <div class="bg-white rounded-2xl border border-pink-100 p-5 shadow-sm border-l-4 border-l-pink-400">
            <p id="rep-resolution-rate" class="text-3xl font-bold text-pink-600">-%</p>
            <p class="text-sm text-gray-500 mt-1">Tingkat Selesai</p>
        </div>
        <div class="bg-white rounded-2xl border border-blue-100 p-5 shadow-sm border-l-4 border-l-blue-400">
            <p id="rep-avg-time" class="text-3xl font-bold text-blue-600">-</p>
            <p class="text-sm text-gray-500 mt-1">Rata-rata (jam)</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- By Kategori --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-900">Pengaduan per Kategori</h2>
            </div>
            <div id="kategori-table" class="divide-y divide-gray-50 min-h-[200px] flex items-center justify-center">
                <div class="text-sm text-gray-400 py-8">Memuat data...</div>
            </div>
        </div>

        {{-- By Status --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-900">Distribusi Status</h2>
            </div>
            <div id="status-table" class="divide-y divide-gray-50 min-h-[200px] flex items-center justify-center">
                <div class="text-sm text-gray-400 py-8">Memuat data...</div>
            </div>
        </div>
    </div>

    {{-- Loading overlay --}}
    <div id="loading" class="hidden fixed inset-0 bg-white bg-opacity-60 flex items-center justify-center z-50">
        <svg class="animate-spin w-8 h-8 text-pink-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
    </div>
</div>

<script>
const statusLabels = { pending: 'Pending', verified: 'Terverifikasi', in_progress: 'Diproses', resolved: 'Selesai', rejected: 'Ditolak' };
const statusColors = { pending: 'text-yellow-600 bg-yellow-100', verified: 'text-blue-600 bg-blue-100', in_progress: 'text-purple-600 bg-purple-100', resolved: 'text-green-600 bg-green-100', rejected: 'text-red-600 bg-red-100' };

function loadReport(month) {
    document.getElementById('loading').classList.remove('hidden');

    fetch(`{{ route('dashboard.monthly-report') }}?month=${month}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('report-period').textContent = data.period;
        document.getElementById('rep-total').textContent = data.total_received;
        document.getElementById('rep-resolved').textContent = data.total_resolved;
        const rate = data.total_received > 0 ? Math.round((data.total_resolved / data.total_received) * 100) : 0;
        document.getElementById('rep-resolution-rate').textContent = rate + '%';
        const avg = data.avg_resolution_time ? Math.round(data.avg_resolution_time * 10) / 10 : 0;
        document.getElementById('rep-avg-time').textContent = avg;

        // Kategori table
        const kt = document.getElementById('kategori-table');
        if (data.by_kategori && data.by_kategori.length > 0) {
            kt.className = 'divide-y divide-gray-50';
            kt.innerHTML = data.by_kategori.map(k => `
                <div class="flex items-center justify-between px-6 py-3.5">
                    <span class="text-sm text-gray-700">${k.nama}</span>
                    <div class="flex items-center gap-3">
                        <div class="w-20 bg-gray-100 rounded-full h-1.5">
                            <div class="bg-pink-400 h-1.5 rounded-full" style="width: ${data.total_received > 0 ? Math.round((k.pengaduan_count / data.total_received) * 100) : 0}%"></div>
                        </div>
                        <span class="text-sm font-bold text-gray-900 w-6 text-right">${k.pengaduan_count}</span>
                    </div>
                </div>
            `).join('');
        } else {
            kt.className = 'flex items-center justify-center min-h-[200px]';
            kt.innerHTML = '<p class="text-sm text-gray-400 py-8">Tidak ada data</p>';
        }

        // Status table
        const st = document.getElementById('status-table');
        if (data.by_status && data.by_status.length > 0) {
            st.className = 'divide-y divide-gray-50';
            st.innerHTML = data.by_status.map(s => `
                <div class="flex items-center justify-between px-6 py-3.5">
                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold ${statusColors[s.status] || 'text-gray-600 bg-gray-100'}">${statusLabels[s.status] || s.status}</span>
                    <span class="text-sm font-bold text-gray-900">${s.count}</span>
                </div>
            `).join('');
        } else {
            st.className = 'flex items-center justify-center min-h-[200px]';
            st.innerHTML = '<p class="text-sm text-gray-400 py-8">Tidak ada data</p>';
        }
    })
    .catch(() => {
        document.getElementById('loading').classList.add('hidden');
    });
}

// Load current month
loadReport('{{ date("Y-m") }}');
</script>
@endsection
