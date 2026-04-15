<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use App\Models\KategoriPengaduan;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PengaduanController extends Controller
{
    /**
     * Display a listing of pengaduan with advanced filtering
     */
    public function index(Request $request)
    {
        $query = Pengaduan::with(['kategori', 'user', 'assignedUser']);

        // Role-based access control
        $user = auth()->user();
        if ($user && !$user->isAdmin()) {
            if ($user->isPetugas()) {
                $query->where('assigned_to', $user->id);
            } else {
                $query->where('user_id', $user->id);
            }
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->status($request->status);
        }

        // Filter by kategori
        if ($request->filled('kategori_id')) {
            $query->kategori($request->kategori_id);
        }

        // Filter by tingkat urgensi
        if ($request->filled('urgensi')) {
            $query->where('tingkat_urgensi', $request->urgensi);
        }

        // Filter by assigned user
        if ($request->filled('assigned_to')) {
            $query->assignedTo($request->assigned_to);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        // Filter by SLA status
        if ($request->filled('sla_status')) {
            if ($request->sla_status === 'overdue') {
                $query->overdueSla();
            }
        }

        // Filter anonim
        if ($request->filled('is_anonim')) {
            $query->where('is_anonim', $request->is_anonim);
        }

        // Sorting
        $sortField = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        $allowedSortFields = ['created_at', 'updated_at', 'status', 'tingkat_urgensi', 'kode_unik'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $pengaduan = $query->paginate($perPage);

        // Get statistics for dashboard
        $stats = $this->getStatistics();

        return view('pengaduan.index', compact('pengaduan', 'stats'));
    }

    /**
     * Show the form for creating a new pengaduan
     */
    public function create()
    {
        $kategoris = KategoriPengaduan::active()->ordered()->get();
        return view('pengaduan.create', compact('kategoris'));
    }

    /**
     * Store a newly created pengaduan
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori_pengaduan,id',
            'judul' => 'required|string|max:200',
            'deskripsi' => 'required|string',
            'tingkat_urgensi' => 'required|in:rendah,sedang,tinggi,darurat',
            'is_anonim' => 'boolean',
            'nama_pelapor' => 'required_if:is_anonim,false|nullable|string|max:100',
            'email_pelapor' => 'required_if:is_anonim,false|nullable|email|max:100',
            'telp_pelapor' => 'nullable|string|max:20',
            'lokasi_kejadian' => 'nullable|string|max:200',
            'tanggal_kejadian' => 'nullable|date',
            'bukti_foto.*' => 'nullable|image|max:5120', // Max 5MB per file
        ]);

        DB::beginTransaction();
        try {
            // Handle file upload
            $buktiFoto = [];
            if ($request->hasFile('bukti_foto')) {
                foreach ($request->file('bukti_foto') as $file) {
                    $path = $file->store('pengaduan/bukti', 'public');
                    $buktiFoto[] = $path;
                }
            }

            // Create pengaduan
            $pengaduan = Pengaduan::create([
                'kategori_id' => $validated['kategori_id'],
                'judul' => $validated['judul'],
                'deskripsi' => $validated['deskripsi'],
                'tingkat_urgensi' => $validated['tingkat_urgensi'],
                'is_anonim' => $request->boolean('is_anonim'),
                'user_id' => auth()->id(), // Simpan ID user asli meskipun anonim (untuk Admin)
                'nama_pelapor' => $validated['nama_pelapor'] ?? (auth()->check() ? auth()->user()->name : null),
                'email_pelapor' => $validated['email_pelapor'] ?? (auth()->check() ? auth()->user()->email : null),
                'telp_pelapor' => $validated['telp_pelapor'] ?? null,
                'lokasi_kejadian' => $validated['lokasi_kejadian'] ?? null,
                'tanggal_kejadian' => $validated['tanggal_kejadian'] ?? null,
                'bukti_foto' => $buktiFoto,
                'status' => 'pending',
            ]);

            // Auto-assign berdasarkan kategori
            $kategori = KategoriPengaduan::find($validated['kategori_id']);
            if ($kategori->pic_default_id) {
                $pengaduan->assignTo($kategori->pic_default_id, 'Auto-assigned berdasarkan kategori');
            }

            // Create timeline entry
            $pengaduan->timeline()->create([
                'status' => 'pending',
                'catatan' => 'Pengaduan dibuat',
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            ActivityLog::log('buat_pengaduan', $pengaduan->kode_unik, $pengaduan->judul);

            return redirect()
                ->route('pengaduan.show', $pengaduan->kode_unik)
                ->with('success', 'Pengaduan berhasil dibuat dengan kode: ' . $pengaduan->kode_unik);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded files if transaction fails
            foreach ($buktiFoto as $path) {
                Storage::disk('public')->delete($path);
            }

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified pengaduan
     */
    public function show($kode_unik)
    {
        $pengaduan = Pengaduan::with(['kategori', 'user', 'assignedUser', 'timeline.updatedBy'])
            ->where('kode_unik', $kode_unik)
            ->firstOrFail();

        // Check authorization
        if (!$this->canViewPengaduan($pengaduan)) {
            abort(403, 'Unauthorized access');
        }

        return view('pengaduan.show', compact('pengaduan'));
    }

    /**
     * Show the form for editing pengaduan
     */
    public function edit(Pengaduan $pengaduan)
    {
        \Illuminate\Support\Facades\Gate::authorize('update', $pengaduan);
        
        $kategoris = KategoriPengaduan::active()->ordered()->get();
        $petugas = User::petugas()->active()->get();
        
        return view('pengaduan.edit', compact('pengaduan', 'kategoris', 'petugas'));
    }

    /**
     * Update the specified pengaduan
     */
    public function update(Request $request, Pengaduan $pengaduan)
    {
        \Illuminate\Support\Facades\Gate::authorize('update', $pengaduan);

        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori_pengaduan,id',
            'judul' => 'required|string|max:200',
            'deskripsi' => 'required|string',
            'tingkat_urgensi' => 'required|in:rendah,sedang,tinggi,darurat',
            'status' => 'required|in:pending,verified,in_progress,resolved,rejected',
            'assigned_to' => 'nullable|exists:users,id',
            'catatan_internal' => 'nullable|string',
            'solusi' => 'nullable|string',
            'bukti_penyelesaian.*' => 'nullable|image|max:5120',
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $pengaduan->status;

            // Handle bukti penyelesaian upload
            $buktiPenyelesaian = $pengaduan->bukti_penyelesaian ?? [];
            if ($request->hasFile('bukti_penyelesaian')) {
                foreach ($request->file('bukti_penyelesaian') as $file) {
                    $path = $file->store('pengaduan/penyelesaian', 'public');
                    $buktiPenyelesaian[] = $path;
                }
            }

            $pengaduan->update([
                'kategori_id' => $validated['kategori_id'],
                'judul' => $validated['judul'],
                'deskripsi' => $validated['deskripsi'],
                'tingkat_urgensi' => $validated['tingkat_urgensi'],
                'status' => $validated['status'],
                'assigned_to' => $validated['assigned_to'] ?? null,
                'catatan_internal' => $validated['catatan_internal'] ?? null,
                'solusi' => $validated['solusi'] ?? null,
                'bukti_penyelesaian' => $buktiPenyelesaian,
            ]);

            // Add timeline if status changed
            if ($oldStatus !== $validated['status']) {
                $pengaduan->timeline()->create([
                    'status' => $validated['status'],
                    'catatan' => $request->catatan_perubahan ?? 'Status diubah',
                    'updated_by' => auth()->id(),
                ]);

                event(new \App\Events\StatusPengaduanBerubah($pengaduan, $validated['status']));
                ActivityLog::log('update_status_pengaduan', $pengaduan->kode_unik, "Status: {$validated['status']}");
            }

            DB::commit();

            return redirect()
                ->route('pengaduan.show', $pengaduan->kode_unik)
                ->with('success', 'Pengaduan berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update status pengaduan (simplified)
     */
    public function updateStatus(Request $request, Pengaduan $pengaduan)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,verified,in_progress,resolved,rejected',
            'catatan' => 'nullable|string',
            'solusi' => 'nullable|string|required_if:status,resolved',
            'bukti_penyelesaian.*' => 'nullable|image|max:5120', // Max 5MB
        ]);

        if ($request->has('solusi')) {
            $pengaduan->solusi = $validated['solusi'];
        }

        if ($request->hasFile('bukti_penyelesaian')) {
            $pathNames = [];
            foreach ($request->file('bukti_penyelesaian') as $file) {
                $pathNames[] = $file->store('pengaduan/penyelesaian', 'public');
            }
            $pengaduan->bukti_penyelesaian = $pathNames;
        }

        $pengaduan->updateStatus($validated['status'], $validated['catatan'] ?? null, auth()->id());
        
        ActivityLog::log('update_status_pengaduan', $pengaduan->kode_unik, "Status: {$validated['status']}" . ($request->has('solusi') ? " | Solusi ditambahkan" : ""));

        return back()->with('success', 'Status pengaduan berhasil diperbarui');
    }

    /**
     * Assign pengaduan to user
     */
    public function assign(Request $request, Pengaduan $pengaduan)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'catatan' => 'nullable|string',
        ]);

        $pengaduan->assignTo($validated['assigned_to'], $validated['catatan'] ?? null);

        return back()->with('success', 'Pengaduan berhasil ditugaskan');
    }

    /**
     * Soft delete pengaduan
     */
    public function destroy(Pengaduan $pengaduan)
    {
        \Illuminate\Support\Facades\Gate::authorize('delete', $pengaduan);
        
        $kode = $pengaduan->kode_unik;
        $pengaduan->delete();
        ActivityLog::log('hapus_pengaduan', $kode);

        return redirect()
            ->route('pengaduan.index')
            ->with('success', 'Pengaduan berhasil dihapus');
    }

    /**
     * Get statistics for dashboard
     */
    private function getStatistics()
    {
        return [
            'total' => Pengaduan::count(),
            'pending' => Pengaduan::pending()->count(),
            'verified' => Pengaduan::verified()->count(),
            'in_progress' => Pengaduan::inProgress()->count(),
            'resolved' => Pengaduan::resolved()->count(),
            'rejected' => Pengaduan::rejected()->count(),
            'urgent' => Pengaduan::urgent()->whereNotIn('status', ['resolved', 'rejected'])->count(),
            'overdue_sla' => Pengaduan::overdueSla()->count(),
        ];
    }

    /**
     * Check if user can view pengaduan
     */
    private function canViewPengaduan($pengaduan)
    {
        // Admin can view all
        if (auth()->check() && auth()->user()->isAdmin()) {
            return true;
        }

        // Petugas can view assigned pengaduan
        if (auth()->check() && $pengaduan->assigned_to === auth()->id()) {
            return true;
        }

        // Owner can view their own pengaduan
        if (auth()->check() && $pengaduan->user_id === auth()->id()) {
            return true;
        }

        // Anyone can view with kode_unik (for anonim tracking)
        return true;
    }

    /**
     * Track pengaduan by kode unik (for public/anonim)
     */
    public function track(Request $request)
    {
        // If GET request (no kode_unik), just show the search form
        if ($request->isMethod('GET') && !$request->filled('kode_unik')) {
            return view('track.index');
        }

        $validated = $request->validate([
            'kode_unik' => 'required|string',
        ]);

        $pengaduan = Pengaduan::with(['kategori', 'timeline', 'assignedUser'])
            ->where('kode_unik', $validated['kode_unik'])
            ->first();

        if (!$pengaduan) {
            return back()->with('error', 'Kode pengaduan tidak ditemukan');
        }

        return view('track.index', compact('pengaduan'));
    }

    /**
     * Export pengaduan data
     */
    public function export(Request $request)
    {
        // This will be implemented with Excel/PDF export
        // For now, return JSON
        $query = Pengaduan::with(['kategori', 'user', 'assignedUser']);
        
        // Apply same filters as index
        if ($request->filled('status')) {
            $query->status($request->status);
        }
        
        $data = $query->get();
        
        return response()->json($data);
    }
}