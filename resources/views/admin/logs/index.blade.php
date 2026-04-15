@extends('layout.dashboard')

@section('title', 'Log Aktivitas Admin - SIPS')

@section('content')
<div class="py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Log Aktivitas</h1>
            <p class="text-sm text-gray-500 mt-1">Riwayat aksi yang dilakukan oleh pengguna di sistem</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.logs.index') }}" class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6 flex flex-wrap items-end gap-4">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1.5 ml-1">Cari Aksi</label>
            <input type="text" name="action" value="{{ request('action') }}" placeholder="Contoh: login, update..." class="w-full px-4 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300">
        </div>
        <button type="submit" class="px-6 py-2 bg-gray-900 text-white text-sm font-bold rounded-xl hover:bg-gray-800 transition-colors">
            Filter
        </button>
        <a href="{{ route('admin.logs.index') }}" class="px-4 py-2 text-gray-500 text-sm font-bold hover:text-gray-700 transition-colors">Reset</a>
    </form>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Target</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Detail</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="text-sm font-medium text-gray-900">{{ $log->created_at->format('d/m/Y') }}</span>
                            <p class="text-[11px] text-gray-400 mt-0.5">{{ $log->created_at->format('H:i:s') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500 overflow-hidden">
                                     @if($log->user && $log->user->avatar)
                                        <img src="{{ Storage::url($log->user->avatar) }}" alt="">
                                     @else
                                        {{ substr($log->user->name ?? '?', 0, 1) }}
                                     @endif
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $log->user->name ?? 'Guest/System' }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $log->user->role ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $badgeClass = match($log->action) {
                                    'login' => 'bg-blue-50 text-blue-600 border-blue-100',
                                    'hapus_user' => 'bg-red-50 text-red-600 border-red-100',
                                    'tambah_user' => 'bg-green-50 text-green-600 border-green-100',
                                    'update_user' => 'bg-yellow-50 text-yellow-600 border-yellow-100',
                                    'menonaktifkan_user' => 'bg-orange-50 text-orange-600 border-orange-100',
                                    'tambah_departemen' => 'bg-green-50 text-green-600 border-green-100',
                                    'update_departemen' => 'bg-yellow-50 text-yellow-600 border-yellow-100',
                                    'hapus_departemen' => 'bg-red-50 text-red-600 border-red-100',
                                    'tambah_kategori' => 'bg-green-50 text-green-600 border-green-100',
                                    'update_kategori' => 'bg-yellow-50 text-yellow-600 border-yellow-100',
                                    'hapus_kategori' => 'bg-red-50 text-red-600 border-red-100',
                                    'buat_pengaduan' => 'bg-blue-50 text-blue-600 border-blue-100',
                                    'update_status_pengaduan' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                    'hapus_pengaduan' => 'bg-red-50 text-red-600 border-red-100',
                                    default => 'bg-gray-50 text-gray-600 border-gray-100'
                                };
                            @endphp
                            <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg border {{ $badgeClass }}">
                                {{ strtoupper($log->action) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-semibold text-gray-700">{{ $log->target ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-xs text-gray-500 max-w-[200px] truncate" title="{{ $log->details }}">
                                {{ $log->details ?? '-' }}
                            </p>
                        </td>
                        <td class="px-6 py-4">
                            <code class="text-[11px] text-gray-400 bg-gray-50 px-2 py-0.5 rounded-lg">{{ $log->ip_address }}</code>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <p class="text-sm text-gray-400">Belum ada data log aktivitas</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    /* Custom pagination styling to match UI */
    .pagination { display: flex; gap: 4px; }
    .page-item.active .page-link { background: #cc2c6b; border-color: #cc2c6b; color: white; }
    .page-link { border-radius: 8px; font-size: 0.8rem; font-weight: 600; padding: 6px 12px; color: #64748b; border: 1px solid #f1f5f9; transition: all 0.2s; }
    .page-link:hover { background: #f8fafc; color: #cc2c6b; }
</style>
@endsection

