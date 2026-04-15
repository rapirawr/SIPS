<?php

namespace App\Http\Controllers;

use App\Models\KategoriPengaduan;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class KategoriPengaduanController extends Controller
{
    /**
     * Display a listing of kategori
     */
    public function index()
    {
        $kategoris = KategoriPengaduan::withCount('pengaduan')
            ->with('picDefault')
            ->ordered()
            ->get();

        return view('kategori.index', compact('kategoris'));
    }

    /**
     * Show the form for creating a new kategori
     */
    public function create()
    {
        $petugas = User::petugas()->active()->get();
        return view('kategori.create', compact('petugas'));
    }

    /**
     * Store a newly created kategori
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:kategori_pengaduan,nama',
            'deskripsi' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'warna' => 'required|string|max:20',
            'sla_hours' => 'required|integer|min:1|max:720',
            'pic_default_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
            'urutan' => 'nullable|integer',
        ]);

        $kategori = KategoriPengaduan::create($validated);
        ActivityLog::log('tambah_kategori', $kategori->nama, $kategori->deskripsi);

        return redirect()
            ->route('kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    /**
     * Display the specified kategori
     */
    public function show(KategoriPengaduan $kategori)
    {
        $kategori->load(['pengaduan' => function ($query) {
            $query->latest()->limit(10);
        }]);

        $stats = [
            'total' => $kategori->pengaduan()->count(),
            'pending' => $kategori->pengaduan()->pending()->count(),
            'resolved' => $kategori->pengaduan()->resolved()->count(),
        ];

        return view('kategori.show', compact('kategori', 'stats'));
    }

    /**
     * Show the form for editing kategori
     */
    public function edit(KategoriPengaduan $kategori)
    {
        $petugas = User::petugas()->active()->get();
        return view('kategori.edit', compact('kategori', 'petugas'));
    }

    /**
     * Update the specified kategori
     */
    public function update(Request $request, KategoriPengaduan $kategori)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:kategori_pengaduan,nama,' . $kategori->id,
            'deskripsi' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'warna' => 'required|string|max:20',
            'sla_hours' => 'required|integer|min:1|max:720',
            'pic_default_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
            'urutan' => 'nullable|integer',
        ]);

        $kategori->update($validated);
        ActivityLog::log('update_kategori', $kategori->nama, "Update info kategori");

        return redirect()
            ->route('kategori.index')
            ->with('success', 'Kategori berhasil diperbarui');
    }

    /**
     * Remove the specified kategori
     */
    public function destroy(KategoriPengaduan $kategori)
    {
        // Check if kategori has pengaduan
        if ($kategori->pengaduan()->count() > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki pengaduan');
        }

        $nama = $kategori->nama;
        $kategori->delete();
        ActivityLog::log('hapus_kategori', $nama);

        return redirect()
            ->route('kategori.index')
            ->with('success', 'Kategori berhasil dihapus');
    }

    /**
     * Toggle active status
     */
    public function toggleActive(KategoriPengaduan $kategori)
    {
        $status = $kategori->is_active ? 'mengaktifkan' : 'menonaktifkan';
        ActivityLog::log("{$status}_kategori", $kategori->nama);

        return back()->with('success', 'Status kategori berhasil diubah');
    }
}