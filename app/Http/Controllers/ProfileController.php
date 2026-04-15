<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\ActivityLog;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // Load user statistics
        $stats = [
            'total_pengaduan' => $user->pengaduan()->count(),
            'pending_pengaduan' => $user->pengaduan()->pending()->count(),
            'resolved_pengaduan' => $user->pengaduan()->resolved()->count(),
            'unread_notifications' => $user->unread_notifications_count,
        ];

        // For petugas, add assigned stats
        if ($user->isPetugas()) {
            $stats['assigned_total'] = $user->pengaduanAssigned()->count();
            $stats['assigned_pending'] = $user->pending_assigned_count;
            $stats['assigned_resolved'] = $user->pengaduanAssigned()->resolved()->count();
        }

        return view('profile.edit', compact('user', 'stats'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            'telp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'max:2048'], // Max 2MB
        ]);

        $user = $request->user();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        $user->fill($validated);

        // If email changed, mark as unverified
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
        ActivityLog::log('update_profil', $user->name, "Update data profil mandiri");

        return back()->with('success', 'Profile berhasil diperbarui');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        ActivityLog::log('update_password', $request->user()->name, "Update password mandiri");

        return back()->with('success', 'Password berhasil diperbarui');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Check if user has active pengaduan
        if ($user->pengaduan()->whereNotIn('status', ['resolved', 'rejected'])->count() > 0) {
            return back()->with('error', 'Anda tidak dapat menghapus akun karena masih memiliki pengaduan aktif');
        }

        // For petugas, check assigned pengaduan
        if ($user->isPetugas() && $user->pengaduanAssigned()->whereNotIn('status', ['resolved', 'rejected'])->count() > 0) {
            return back()->with('error', 'Anda tidak dapat menghapus akun karena masih memiliki pengaduan yang ditugaskan');
        }

        Auth::logout();

        // Delete avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Akun berhasil dihapus');
    }

    /**
     * Upload or update avatar
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'avatar' => ['required', 'image', 'max:2048'], // Max 2MB
        ]);

        $user = $request->user();

        // Delete old avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $avatarPath = $request->file('avatar')->store('avatars', 'public');

        $user->update(['avatar' => $avatarPath]);

        return back()->with('success', 'Avatar berhasil diperbarui');
    }

    /**
     * Remove avatar
     */
    public function removeAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        return back()->with('success', 'Avatar berhasil dihapus');
    }

    /**
     * Display user activity log
     */
    public function activity(Request $request): View
    {
        $user = $request->user();

        // Get recent pengaduan
        $recentPengaduan = $user->pengaduan()
            ->with('kategori')
            ->latest()
            ->limit(10)
            ->get();

        // Get recent assigned pengaduan (for petugas)
        $assignedPengaduan = null;
        if ($user->isPetugas()) {
            $assignedPengaduan = $user->pengaduanAssigned()
                ->with('kategori')
                ->latest()
                ->limit(10)
                ->get();
        }

        // Get recent notifications
        $notifications = $user->notifikasi()
            ->with('pengaduan')
            ->latest()
            ->limit(10)
            ->get();

        return view('profile.activity', compact('recentPengaduan', 'assignedPengaduan', 'notifications'));
    }

    /**
     * Update notification preferences
     */
    public function updateNotificationPreferences(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email_notifications' => ['boolean'],
            'sms_notifications' => ['boolean'],
            'push_notifications' => ['boolean'],
        ]);

        // This would typically update a preferences table or JSON column
        // For now, we'll just return success
        // You can extend the users table with a 'preferences' JSON column

        return back()->with('success', 'Preferensi notifikasi berhasil diperbarui');
    }

    /**
     * Export user data (GDPR compliance)
     */
    public function exportData(Request $request)
    {
        $user = $request->user();

        $data = [
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'department' => $user->department,
                'telp' => $user->telp,
                'alamat' => $user->alamat,
                'created_at' => $user->created_at,
            ],
            'pengaduan' => $user->pengaduan()->get()->map(function ($p) {
                return [
                    'kode_unik' => $p->kode_unik,
                    'judul' => $p->judul,
                    'kategori' => $p->kategori->nama,
                    'status' => $p->status,
                    'created_at' => $p->created_at,
                    'resolved_at' => $p->resolved_at,
                ];
            }),
            'notifications' => $user->notifikasi()->get()->map(function ($n) {
                return [
                    'judul' => $n->judul,
                    'pesan' => $n->pesan,
                    'tipe' => $n->tipe,
                    'created_at' => $n->created_at,
                ];
            }),
        ];

        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="user_data_' . $user->id . '.json"');
    }
}