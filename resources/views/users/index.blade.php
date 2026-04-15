@extends('layout.dashboard')

@section('title', 'Manajemen User - SIPS - Sarana Informasi Pengaduan Sekolah')

@section('content')
<div class="py-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen User</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola pengguna sarana pengaduan</p>
        </div>
        <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-white text-sm font-bold rounded-xl transition-all hover:-translate-y-0.5 hover:shadow-lg" style="background: linear-gradient(135deg, #cc2c6b, #374151);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah User
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
        @php
        $statCards = [
            ['label' => 'Total', 'value' => $stats['total'], 'color' => 'text-gray-700', 'bg' => 'bg-gray-50'],
            ['label' => 'Aktif', 'value' => $stats['active'], 'color' => 'text-green-600', 'bg' => 'bg-green-50'],
            ['label' => 'Admin', 'value' => $stats['admin'], 'color' => 'text-pink-600', 'bg' => 'bg-pink-50'],
            ['label' => 'Petugas', 'value' => $stats['petugas'], 'color' => 'text-blue-600', 'bg' => 'bg-blue-50'],
            ['label' => 'User', 'value' => $stats['user'], 'color' => 'text-purple-600', 'bg' => 'bg-purple-50'],
        ];
        @endphp
        @foreach($statCards as $s)
        <div class="{{ $s['bg'] }} rounded-xl p-3 text-center">
            <p class="text-2xl font-bold {{ $s['color'] }}">{{ $s['value'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5 font-medium">{{ $s['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('users.index') }}" class="bg-white rounded-2xl border border-gray-100 p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="lg:col-span-2 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, departemen..." class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300">
            </div>
            <select name="role" class="text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-300 bg-white">
                <option value="">Semua Role</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="kepala_sekolah" {{ request('role') == 'kepala_sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                <option value="koordinator" {{ request('role') == 'koordinator' ? 'selected' : '' }}>Koordinator</option>
                <option value="petugas" {{ request('role') == 'petugas' ? 'selected' : '' }}>Petugas</option>
                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
            </select>
            <select name="is_active" class="text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-300 bg-white">
                <option value="">Semua Status</option>
                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <select name="department_id" class="text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-300 bg-white">
                <option value="">Semua Departemen</option>
                @foreach($departments as $dept)
                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2.5 text-white text-sm font-semibold rounded-xl" style="background: linear-gradient(135deg, #cc2c6b, #374151);">Filter</button>
                <a href="{{ route('users.index') }}" class="px-4 py-2.5 text-sm font-semibold text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">Reset</a>
            </div>
        </div>
    </form>

    {{-- Bulk Action Form --}}
    <form id="bulk-form" action="{{ route('users.bulk-action') }}" method="POST">
        @csrf
        <input type="hidden" name="action" id="bulk-action-value">

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            {{-- Bulk Actions Bar --}}
            <div id="bulk-bar" class="hidden px-5 py-3 bg-pink-50 border-b border-pink-100 flex items-center justify-between">
                <span class="text-sm text-pink-700 font-medium"><span id="selected-count">0</span> user dipilih</span>
                <div class="flex gap-2">
                    <button type="button" onclick="submitBulk('activate')" class="px-3 py-1.5 text-xs font-semibold text-green-700 bg-green-100 rounded-lg hover:bg-green-200 transition-colors">Aktifkan</button>
                    <button type="button" onclick="submitBulk('deactivate')" class="px-3 py-1.5 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded-lg hover:bg-yellow-200 transition-colors">Nonaktifkan</button>
                    <button type="button" onclick="submitBulk('delete')" class="px-3 py-1.5 text-xs font-semibold text-red-700 bg-red-100 rounded-lg hover:bg-red-200 transition-colors">Hapus</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="px-5 py-3.5 text-left w-10">
                                <input type="checkbox" id="select-all" class="w-4 h-4 rounded text-pink-600 border-gray-300 focus:ring-pink-300" onchange="toggleAll(this)">
                            </th>
                            <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">User</th>
                            <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Role</th>
                            <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Departemen</th>
                            <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Status</th>
                            <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Bergabung</th>
                            <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-4">
                                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox w-4 h-4 rounded text-pink-600 border-gray-300 focus:ring-pink-300" onchange="updateBulkBar()">
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-9 h-9 rounded-full object-cover flex-shrink-0">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-900 truncate">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 hidden md:table-cell">
                                @php
                                $roleBadge = [
                                    'admin' => 'bg-pink-100 text-pink-700',
                                    'kepala_sekolah' => 'bg-purple-100 text-purple-700',
                                    'koordinator' => 'bg-blue-100 text-blue-700',
                                    'petugas' => 'bg-cyan-100 text-cyan-700',
                                    'user' => 'bg-gray-100 text-gray-600',
                                ];
                                $roleLabel = [
                                    'admin' => 'Admin',
                                    'kepala_sekolah' => 'Kepala Sekolah',
                                    'koordinator' => 'Koordinator',
                                    'petugas' => 'Petugas',
                                    'user' => 'User',
                                ];
                                @endphp
                                <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-semibold {{ $roleBadge[$user->role] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $roleLabel[$user->role] ?? $user->role }}
                                </span>
                            </td>
                            <td class="px-5 py-4 hidden lg:table-cell text-gray-600 text-xs">{{ $user->departmentData->name ?? ($user->department ?? '-') }}</td>
                            <td class="px-5 py-4 hidden md:table-cell">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 hidden lg:table-cell text-gray-500 text-xs">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('users.show', $user) }}" class="p-1.5 text-gray-400 hover:text-pink-600 hover:bg-pink-50 rounded-lg transition-colors" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('users.toggle-active', $user) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-{{ $user->is_active ? 'yellow' : 'green' }}-600 hover:bg-{{ $user->is_active ? 'yellow' : 'green' }}-50 rounded-lg transition-colors" title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            @if($user->is_active)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @endif
                                        </button>
                                    </form>
                                    @if($user->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus user {{ $user->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-16 text-center">
                                <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p class="text-gray-400 font-medium">Tidak ada user ditemukan</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $users->appends(request()->except('page'))->links() }}
            </div>
            @endif
        </div>
    </form>
</div>

<script>
function toggleAll(checkbox) {
    document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = checkbox.checked);
    updateBulkBar();
}

function updateBulkBar() {
    const checked = document.querySelectorAll('.user-checkbox:checked').length;
    document.getElementById('selected-count').textContent = checked;
    document.getElementById('bulk-bar').classList.toggle('hidden', checked === 0);
    document.getElementById('select-all').checked = checked === document.querySelectorAll('.user-checkbox').length && checked > 0;
}

function submitBulk(action) {
    if (!confirm(`Yakin ingin melakukan aksi "${action}" pada user yang dipilih?`)) return;
    document.getElementById('bulk-action-value').value = action;
    document.getElementById('bulk-form').submit();
}
</script>
@endsection

