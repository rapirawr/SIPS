@extends('layout.dashboard')

@section('title', 'Edit Departemen - SIPS')

@section('content')
<div class="py-8">
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('departments.index') }}" class="p-2 text-gray-400 hover:text-gray-600 transition-colors bg-white rounded-xl border border-gray-100 shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Departemen</h1>
            <p class="text-sm text-gray-500 mt-1">Sesuaikan informasi departemen {{ $department->name }}</p>
        </div>
    </div>

    <form action="{{ route('departments.update', $department) }}" method="POST" class="max-w-2xl bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
        @csrf
        @method('PUT')
        <div class="space-y-6">
            <div>
                <label for="name" class="block text-xs font-bold text-gray-400 uppercase mb-2 ml-1">Nama Departemen</label>
                <input type="text" name="name" id="name" value="{{ old('name', $department->name) }}" placeholder="Contoh: Kurikulum / Sarpras" class="w-full px-4 py-3 text-sm border border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-pink-50 transition-all @error('name') border-red-300 @enderror" required>
                @error('name')
                    <p class="text-xs text-red-500 mt-1.5 ml-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-xs font-bold text-gray-400 uppercase mb-2 ml-1">Deskripsi (Opsional)</label>
                <textarea name="description" id="description" rows="4" placeholder="Jelaskan tupoksi departemen ini..." class="w-full px-4 py-3 text-sm border border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-pink-50 transition-all shadow-inner-sm">{{ old('description', $department->description) }}</textarea>
                @error('description')
                    <p class="text-xs text-red-500 mt-1.5 ml-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 p-4 bg-gray-50/50 rounded-2xl border border-gray-100">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $department->is_active) ? 'checked' : '' }} class="w-4 h-4 text-pink-600 rounded border-gray-300 focus:ring-pink-500">
                <label for="is_active" class="text-sm font-bold text-gray-700">Departemen dalam kondisi aktif</label>
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="submit" class="btn-primary px-8 py-3 shadow-lg shadow-pink-200/50">
                    Perbarui Departemen
                </button>
                <a href="{{ route('departments.index') }}" class="px-8 py-3 text-gray-500 text-sm font-bold hover:text-gray-700 transition-colors">
                    Batalkan
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
