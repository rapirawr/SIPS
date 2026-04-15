@extends('layout.dashboard')

@section('title', 'Manajemen Departemen - SIPS')

@section('content')
<div class="py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Departemen</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola departemen atau unit kerja di lingkungan sekolah</p>
        </div>
        <a href="{{ route('departments.create') }}" class="btn-primary px-6 py-3 shadow-lg shadow-pink-200/50">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.6" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Tambah Departemen
        </a>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-100 rounded-2xl flex items-center gap-3 animate-fade-in">
        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <p class="text-sm font-semibold text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Departemen</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Slug</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($departments as $department)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-gray-900">{{ $department->name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <code class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded-lg">{{ $department->slug }}</code>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-xs text-gray-500 max-w-[300px] truncate">{{ $department->description ?? '-' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($department->is_active)
                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg border bg-green-50 text-green-600 border-green-100">AKTIF</span>
                            @else
                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg border bg-gray-50 text-gray-600 border-gray-100">NON-AKTIF</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('departments.edit', $department) }}" class="p-2 text-gray-400 hover:text-pink-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <div x-data="{ confirming: false }">
                                    <button x-show="!confirming" @click="confirming = true; setTimeout(() => confirming = false, 3000)" type="button" class="p-2 text-gray-400 hover:text-red-500 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                            Belum ada data departemen.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($departments->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $departments->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
