<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
        'department_id',
        'telp',
        'alamat',
        'avatar',
        'is_active',
    ];

    public function departmentData()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi ke pengaduan yang dibuat user
     */
    public function pengaduan()
    {
        return $this->hasMany(Pengaduan::class, 'user_id');
    }

    /**
     * Relasi ke pengaduan yang di-assign ke user
     */
    public function pengaduanAssigned()
    {
        return $this->hasMany(Pengaduan::class, 'assigned_to');
    }

    /**
     * Relasi ke notifikasi
     */
    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'user_id');
    }

    /**
     * Relasi ke activity logs
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    /**
     * Relasi ke kategori yang di-PIC
     */
    public function kategoriPIC()
    {
        return $this->hasMany(KategoriPengaduan::class, 'pic_default_id');
    }

    /**
     * SCOPES
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopePetugas($query)
    {
        return $query->whereIn('role', ['petugas', 'koordinator', 'kepala_sekolah']);
    }

    public function scopeDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Check if user has role
     */
    public function hasRole($role)
    {
        if (is_array($role)) {
            return in_array($this->role, $role);
        }
        return $this->role === $role;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is kepala sekolah
     */
    public function isKepalaSekolah()
    {
        return $this->role === 'kepala_sekolah';
    }

    /**
     * Check if user is petugas
     */
    public function isPetugas()
    {
        return in_array($this->role, ['petugas', 'koordinator', 'kepala_sekolah', 'admin']);
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadNotificationsCountAttribute()
    {
        return $this->notifikasi()->unread()->count();
    }

    /**
     * Get pending assigned pengaduan count
     */
    public function getPendingAssignedCountAttribute()
    {
        return $this->pengaduanAssigned()
            ->whereNotIn('status', ['resolved', 'rejected'])
            ->count();
    }

    /**
     * Get avatar URL
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        // Return default avatar with initials
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }
}