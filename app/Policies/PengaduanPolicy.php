<?php

namespace App\Policies;

use App\Models\Pengaduan;
use App\Models\User;

class PengaduanPolicy
{
    /**
     * Admin dapat melakukan semua
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) {
            return true;
        }
        return null;
    }

    /**
     * Siapa saja yang login bisa view
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * User bisa view pengaduan miliknya, petugas bisa view yang di-assign
     */
    public function view(User $user, Pengaduan $pengaduan): bool
    {
        return $pengaduan->user_id === $user->id
            || $pengaduan->assigned_to === $user->id
            || $user->isPetugas();
    }

    /**
     * Siapa saja bisa buat pengaduan
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Petugas bisa update pengaduan yang di-assign, owner bisa update jika masih pending
     */
    public function update(User $user, Pengaduan $pengaduan): bool
    {
        if ($user->isPetugas()) {
            return true;
        }
        // Pemilik hanya bisa edit jika masih pending
        return $pengaduan->user_id === $user->id && $pengaduan->status === 'pending';
    }

    /**
     * Hanya admin dan pemilik (jika masih pending) yang bisa hapus
     */
    public function delete(User $user, Pengaduan $pengaduan): bool
    {
        return $pengaduan->user_id === $user->id && $pengaduan->status === 'pending';
    }

    public function restore(User $user, Pengaduan $pengaduan): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Pengaduan $pengaduan): bool
    {
        return $user->isAdmin();
    }
}
