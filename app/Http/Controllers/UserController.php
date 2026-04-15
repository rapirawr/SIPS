<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;

use App\Models\ActivityLog;
use App\Models\Department;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::with('departmentData');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhereHas('departmentData', function($dq) use ($search) {
                      $dq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        // Filter by department (Old string)
        if ($request->filled('department')) {
            $query->department($request->department);
        }

        // Filter by department ID (New linked table)
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Sorting
        $sortField = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        $allowedSortFields = ['name', 'email', 'role', 'department', 'created_at'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $users = $query->paginate($perPage);

        // Statistics
        $stats = [
            'total' => User::count(),
            'active' => User::active()->count(),
            'admin' => User::admin()->count(),
            'petugas' => User::petugas()->count(),
            'user' => User::role('user')->count(),
        ];

        // Get all departments for filter
        $departments = Department::active()->get();

        return view('users.index', compact('users', 'stats', 'departments'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = [
            'admin' => 'Administrator',
            'kepala_sekolah' => 'Kepala Sekolah',
            'koordinator' => 'Koordinator',
            'petugas' => 'Petugas',
            'user' => 'User',
        ];

        $departments = Department::active()->get();

        return view('users.create', compact('roles', 'departments'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', Rule::in(['admin', 'kepala_sekolah', 'koordinator', 'petugas', 'user'])],
            'department_id' => ['nullable', 'exists:departments,id'],
            'telp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $newUser = User::create($validated);
        ActivityLog::log('tambah_user', $newUser->name, "Role: {$newUser->role}, Dept: {$newUser->department}");

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load([
            'pengaduan' => fn($q) => $q->latest()->limit(10),
            'pengaduanAssigned' => fn($q) => $q->latest()->limit(10),
        ]);

        $stats = [
            'total_pengaduan' => $user->pengaduan()->count(),
            'pengaduan_resolved' => $user->pengaduan()->resolved()->count(),
            'assigned_total' => $user->pengaduanAssigned()->count(),
            'assigned_pending' => $user->pengaduanAssigned()
                ->whereNotIn('status', ['resolved', 'rejected'])
                ->count(),
        ];

        return view('users.show', compact('user', 'stats'));
    }

    /**
     * Show the form for editing user
     */
    public function edit(User $user)
    {
        $roles = [
            'admin' => 'Administrator',
            'kepala_sekolah' => 'Kepala Sekolah',
            'koordinator' => 'Koordinator',
            'petugas' => 'Petugas',
            'user' => 'User',
        ];

        $departments = Department::active()->get();

        return view('users.edit', compact('user', 'roles', 'departments'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', Rule::in(['admin', 'kepala_sekolah', 'koordinator', 'petugas', 'user'])],
            'department_id' => ['nullable', 'exists:departments,id'],
            'telp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        // Only update password if provided
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active');

        $user->update($validated);
        ActivityLog::log('update_user', $user->name, "Update data profil/role");

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diperbarui');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri');
        }

        // Check if user has pengaduan
        if ($user->pengaduan()->count() > 0) {
            return back()->with('error', 'User tidak dapat dihapus karena memiliki riwayat pengaduan');
        }

        // Check if user has assigned pengaduan
        if ($user->pengaduanAssigned()->whereNotIn('status', ['resolved', 'rejected'])->count() > 0) {
            return back()->with('error', 'User tidak dapat dihapus karena masih memiliki pengaduan yang ditugaskan');
        }

        $name = $user->name;
        $user->delete();
        ActivityLog::log('hapus_user', $name);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus');
    }

    /**
     * Toggle user active status
     */
    public function toggleActive(User $user)
    {
        // Prevent deactivating yourself
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri');
        }

        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'mengaktifkan' : 'menonaktifkan';
        ActivityLog::log("{$status}_user", $user->name);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "User berhasil {$status}");
    }

    /**
     * Bulk action for users
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => ['required', Rule::in(['activate', 'deactivate', 'delete'])],
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['exists:users,id'],
        ]);

        $userIds = $validated['user_ids'];

        // Prevent action on yourself
        if (in_array(Auth::id(), $userIds)) {
            return back()->with('error', 'Anda tidak dapat melakukan aksi pada akun Anda sendiri');
        }

        switch ($validated['action']) {
            case 'activate':
                User::whereIn('id', $userIds)->update(['is_active' => true]);
                $message = 'User berhasil diaktifkan';
                break;

            case 'deactivate':
                User::whereIn('id', $userIds)->update(['is_active' => false]);
                $message = 'User berhasil dinonaktifkan';
                break;

            case 'delete':
                // Check if any user has active pengaduan
                $hasActivePengaduan = User::whereIn('id', $userIds)
                    ->whereHas('pengaduanAssigned', function ($q) {
                        $q->whereNotIn('status', ['resolved', 'rejected']);
                    })
                    ->exists();

                if ($hasActivePengaduan) {
                    return back()->with('error', 'Beberapa user tidak dapat dihapus karena masih memiliki pengaduan aktif');
                }

                User::whereIn('id', $userIds)->delete();
                $message = 'User berhasil dihapus';
                break;
        }

        return back()->with('success', $message);
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password berhasil direset');
    }

    /**
     * Get user performance stats (for API)
     */
    public function performance(User $user)
    {
        $stats = [
            'total_assigned' => $user->pengaduanAssigned()->count(),
            'resolved' => $user->pengaduanAssigned()->resolved()->count(),
            'pending' => $user->pengaduanAssigned()->pending()->count(),
            'in_progress' => $user->pengaduanAssigned()->inProgress()->count(),
            'avg_resolution_time' => $user->pengaduanAssigned()
                ->resolved()
                ->get()
                ->avg(function ($p) {
                    return $p->created_at->diffInHours($p->resolved_at);
                }),
            'resolution_rate' => $user->pengaduanAssigned()->count() > 0
                ? round(($user->pengaduanAssigned()->resolved()->count() / $user->pengaduanAssigned()->count()) * 100, 1)
                : 0,
        ];

        return response()->json($stats);
    }
}