@extends('layout.dashboard')

@section('title', 'Performa - ' . $user->name)

@section('content')
<div class="py-8">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('users.show', $user) }}" class="p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Performa Petugas</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $user->name }}</p>
        </div>
    </div>

    {{-- Profile Summary --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm mb-6">
        <div class="flex items-center gap-4">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-2xl object-cover">
            <div>
                <h2 class="text-lg font-bold text-gray-900">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500">{{ $user->department ?? $user->role }}</p>
                <span class="inline-block mt-1 px-2.5 py-0.5 text-xs font-semibold bg-pink-100 text-pink-700 rounded-lg">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
            </div>
        </div>
    </div>

    {{-- Performance Stats --}}
    <div id="perf-stats" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm text-center col-span-1">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center mx-auto mb-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <p id="stat-total" class="text-2xl font-bold text-gray-900">-</p>
            <p class="text-xs text-gray-500 mt-0.5">Total Ditugaskan</p>
        </div>
        <div class="bg-white rounded-2xl border border-green-100 p-4 shadow-sm text-center border-l-4 border-l-green-400">
            <p id="stat-resolved" class="text-2xl font-bold text-green-600">-</p>
            <p class="text-xs text-gray-500 mt-1">Selesai</p>
        </div>
        <div class="bg-white rounded-2xl border border-yellow-100 p-4 shadow-sm text-center border-l-4 border-l-yellow-400">
            <p id="stat-pending" class="text-2xl font-bold text-yellow-600">-</p>
            <p class="text-xs text-gray-500 mt-1">Pending</p>
        </div>
        <div class="bg-white rounded-2xl border border-purple-100 p-4 shadow-sm text-center border-l-4 border-l-purple-400">
            <p id="stat-inprogress" class="text-2xl font-bold text-purple-600">-</p>
            <p class="text-xs text-gray-500 mt-1">Diproses</p>
        </div>
        <div class="bg-white rounded-2xl border border-pink-100 p-4 shadow-sm text-center border-l-4 border-l-pink-400">
            <p id="stat-rate" class="text-2xl font-bold text-pink-600">-</p>
            <p class="text-xs text-gray-500 mt-1">Tingkat Selesai</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm text-center">
            <p id="stat-avgtime" class="text-2xl font-bold text-gray-700">-</p>
            <p class="text-xs text-gray-500 mt-1">Rata-rata (jam)</p>
        </div>
    </div>

    {{-- Loading state --}}
    <div id="loading-state" class="flex items-center justify-center py-8">
        <svg class="animate-spin w-8 h-8 text-pink-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <span class="ml-3 text-sm text-gray-400">Memuat data performa...</span>
    </div>

    {{-- Performance Chart --}}
    <div id="chart-section" class="hidden bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <h3 class="text-base font-bold text-gray-900 mb-5">Visualisasi Performa</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <canvas id="perfChart" height="200"></canvas>
            </div>
            <div class="space-y-4">
                <div class="p-4 bg-green-50 rounded-xl">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-green-700 font-medium">Pengaduan Selesai</span>
                        <span id="prog-resolved-pct" class="text-sm font-bold text-green-700">0%</span>
                    </div>
                    <div class="mt-2 bg-green-200 rounded-full h-2">
                        <div id="prog-resolved-bar" class="bg-green-500 h-2 rounded-full transition-all duration-700" style="width:0%"></div>
                    </div>
                </div>
                <div class="p-4 bg-yellow-50 rounded-xl">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-yellow-700 font-medium">Pending</span>
                        <span id="prog-pending-pct" class="text-sm font-bold text-yellow-700">0%</span>
                    </div>
                    <div class="mt-2 bg-yellow-200 rounded-full h-2">
                        <div id="prog-pending-bar" class="bg-yellow-500 h-2 rounded-full transition-all duration-700" style="width:0%"></div>
                    </div>
                </div>
                <div class="p-4 bg-purple-50 rounded-xl">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-purple-700 font-medium">Sedang Diproses</span>
                        <span id="prog-inprogress-pct" class="text-sm font-bold text-purple-700">0%</span>
                    </div>
                    <div class="mt-2 bg-purple-200 rounded-full h-2">
                        <div id="prog-inprogress-bar" class="bg-purple-500 h-2 rounded-full transition-all duration-700" style="width:0%"></div>
                    </div>
                </div>
                <div class="p-4 bg-pink-50 rounded-xl border border-pink-100">
                    <p class="text-xs text-gray-500 mb-1">Rata-rata Waktu Penyelesaian</p>
                    <p id="avg-time-display" class="text-2xl font-bold text-pink-600">- jam</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    fetch('{{ route("users.performance", $user) }}', {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('chart-section').classList.remove('hidden');

        document.getElementById('stat-total').textContent = data.total_assigned;
        document.getElementById('stat-resolved').textContent = data.resolved;
        document.getElementById('stat-pending').textContent = data.pending;
        document.getElementById('stat-inprogress').textContent = data.in_progress;
        document.getElementById('stat-rate').textContent = (data.resolution_rate || 0) + '%';
        const avg = data.avg_resolution_time ? Math.round(data.avg_resolution_time * 10) / 10 : 0;
        document.getElementById('stat-avgtime').textContent = avg;
        document.getElementById('avg-time-display').textContent = avg + ' jam';

        const total = data.total_assigned || 1;
        const resolvedPct = Math.round((data.resolved / total) * 100);
        const pendingPct = Math.round((data.pending / total) * 100);
        const inProgressPct = Math.round((data.in_progress / total) * 100);

        setTimeout(() => {
            document.getElementById('prog-resolved-bar').style.width = resolvedPct + '%';
            document.getElementById('prog-pending-bar').style.width = pendingPct + '%';
            document.getElementById('prog-inprogress-bar').style.width = inProgressPct + '%';
        }, 100);
        document.getElementById('prog-resolved-pct').textContent = resolvedPct + '%';
        document.getElementById('prog-pending-pct').textContent = pendingPct + '%';
        document.getElementById('prog-inprogress-pct').textContent = inProgressPct + '%';

        const ctx = document.getElementById('perfChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Selesai', 'Pending', 'Diproses', 'Lainnya'],
                datasets: [{
                    data: [data.resolved, data.pending, data.in_progress, Math.max(0, data.total_assigned - data.resolved - data.pending - data.in_progress)],
                    backgroundColor: ['#22c55e', '#eab308', '#a855f7', '#e5e7eb'],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                cutout: '65%',
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 11 }, usePointStyle: true } }
                }
            }
        });
    })
    .catch(() => {
        document.getElementById('loading-state').innerHTML = '<p class="text-sm text-gray-400">Gagal memuat data performa</p>';
    });
</script>
@endsection
