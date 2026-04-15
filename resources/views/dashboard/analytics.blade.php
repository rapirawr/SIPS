@extends('layout.dashboard')

@section('title', 'Analitik - SIPS - Sistem Informasi Pengaduan Sekolah')

@section('content')
<div class="py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Analitik Pengaduan</h1>
            <p class="text-sm text-gray-500 mt-1">Laporan dan statistik mendalam</p>
        </div>
        <div class="flex items-center gap-3">
            <select id="period-select" onchange="loadAnalytics(this.value)" class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-300 bg-white">
                <option value="7">7 Hari</option>
                <option value="30" selected>30 Hari</option>
                <option value="90">90 Hari</option>
                <option value="365">1 Tahun</option>
            </select>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                ← Dashboard
            </a>
        </div>
    </div>

    {{-- Stats (loaded via AJAX) --}}
    <div id="stats-grid" class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        @foreach(['total' => ['label' => 'Total', 'color' => 'text-gray-900'], 'pending' => ['label' => 'Pending', 'color' => 'text-yellow-600'], 'in_progress' => ['label' => 'Diproses', 'color' => 'text-purple-600'], 'resolved' => ['label' => 'Selesai', 'color' => 'text-green-600']] as $key => $s)
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <p id="stat-{{ $key }}" class="text-3xl font-bold {{ $s['color'] }}">-</p>
            <p class="text-sm text-gray-500 mt-1">{{ $s['label'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Trend Chart --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h2 class="text-base font-bold text-gray-900 mb-5">Tren Pengaduan</h2>
            <canvas id="trendChart" height="200"></canvas>
        </div>

        {{-- Status Pie --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h2 class="text-base font-bold text-gray-900 mb-5">Distribusi Status</h2>
            <canvas id="statusChart" height="200"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Kategori Chart --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h2 class="text-base font-bold text-gray-900 mb-5">Distribusi Kategori</h2>
            <canvas id="kategoriChart" height="200"></canvas>
        </div>

        {{-- Key Metrics --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h2 class="text-base font-bold text-gray-900 mb-5">Metrik Kunci</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-pink-50 rounded-xl">
                    <span class="text-sm text-gray-700 font-medium">Tingkat Penyelesaian</span>
                    <span id="metric-resolution-rate" class="text-xl font-bold text-pink-600">-%</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-blue-50 rounded-xl">
                    <span class="text-sm text-gray-700 font-medium">Rata-rata Penyelesaian</span>
                    <span id="metric-avg-time" class="text-xl font-bold text-blue-600">- jam</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-red-50 rounded-xl">
                    <span class="text-sm text-gray-700 font-medium">Pengaduan Darurat Aktif</span>
                    <span id="metric-urgent" class="text-xl font-bold text-red-600">-</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-orange-50 rounded-xl">
                    <span class="text-sm text-gray-700 font-medium">Batas Waktu Terlampaui</span>
                    <span id="metric-overdue" class="text-xl font-bold text-orange-600">-</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let trendChart, statusChart, kategoriChart;

function loadAnalytics(period) {
    fetch(`{{ route('dashboard.analytics') }}?period=${period}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        // Update stats
        document.getElementById('stat-total').textContent = data.stats.total ?? 0;
        document.getElementById('stat-pending').textContent = data.stats.pending ?? 0;
        document.getElementById('stat-in_progress').textContent = data.stats.in_progress ?? 0;
        document.getElementById('stat-resolved').textContent = data.stats.resolved ?? 0;

        // Key metrics
        document.getElementById('metric-resolution-rate').textContent = (data.stats.resolution_rate ?? 0) + '%';
        document.getElementById('metric-avg-time').textContent = (data.stats.avg_resolution_hours ?? 0) + ' jam';
        document.getElementById('metric-urgent').textContent = data.stats.urgent ?? 0;
        document.getElementById('metric-overdue').textContent = data.stats.overdue_sla ?? 0;

        // Trend Chart
        if (trendChart) trendChart.destroy();
        const tCtx = document.getElementById('trendChart').getContext('2d');
        trendChart = new Chart(tCtx, {
            type: 'line',
            data: {
                labels: data.trend.labels,
                datasets: [
                    { label: 'Total', data: data.trend.total, borderColor: '#cc2c6b', backgroundColor: 'rgba(204, 44, 107,0.08)', fill: true, tension: 0.4, borderWidth: 2.5, pointRadius: 3 },
                    { label: 'Selesai', data: data.trend.resolved, borderColor: '#22c55e', fill: false, tension: 0.4, borderWidth: 2, pointRadius: 3 },
                    { label: 'Pending', data: data.trend.pending, borderColor: '#eab308', fill: false, tension: 0.4, borderWidth: 2, pointRadius: 3, borderDash: [5,5] }
                ]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, usePointStyle: true } } }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });

        // Status Doughnut
        if (statusChart) statusChart.destroy();
        const sCtx = document.getElementById('statusChart').getContext('2d');
        statusChart = new Chart(sCtx, {
            type: 'doughnut',
            data: {
                labels: data.status.labels,
                datasets: [{ data: data.status.values, backgroundColor: ['#fbbf24','#3b82f6','#a855f7','#22c55e','#ef4444'], borderWidth: 0, hoverOffset: 6 }]
            },
            options: { responsive: true, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, usePointStyle: true } } } }
        });

        // Kategori Bar
        if (kategoriChart) kategoriChart.destroy();
        const kCtx = document.getElementById('kategoriChart').getContext('2d');
        kategoriChart = new Chart(kCtx, {
            type: 'bar',
            data: {
                labels: data.kategori.labels,
                datasets: [{ label: 'Pengaduan', data: data.kategori.values, backgroundColor: data.kategori.colors.map(c => c || '#cc2c6b'), borderRadius: 8, borderWidth: 0 }]
            },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });
    })
    .catch(err => console.error('Gagal memuat analitik:', err));
}

// Load on page init
loadAnalytics(30);
</script>
@endsection

