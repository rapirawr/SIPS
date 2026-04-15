<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class KategoriPengaduan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kategori_pengaduan';

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'icon',
        'warna',
        'sla_hours',
        'pic_default_id',
        'is_active',
        'urutan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sla_hours' => 'integer',
        'urutan' => 'integer',
    ];

    /**
     * Boot function untuk auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($kategori) {
            if (empty($kategori->slug)) {
                $kategori->slug = Str::slug($kategori->nama);
            }
        });

        static::updating(function ($kategori) {
            if ($kategori->isDirty('nama') && empty($kategori->slug)) {
                $kategori->slug = Str::slug($kategori->nama);
            }
        });
    }

    /**
     * Relasi ke PIC default
     */
    public function picDefault()
    {
        return $this->belongsTo(User::class, 'pic_default_id');
    }

    /**
     * Relasi ke pengaduan
     */
    public function pengaduan()
    {
        return $this->hasMany(Pengaduan::class, 'kategori_id');
    }

    /**
     * Scope untuk kategori aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk urutan
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan', 'asc')->orderBy('nama', 'asc');
    }

    /**
     * Get total pengaduan
     */
    public function getTotalPengaduanAttribute()
    {
        return $this->pengaduan()->count();
    }

    /**
     * Get pengaduan pending
     */
    public function getPengaduanPendingAttribute()
    {
        return $this->pengaduan()->where('status', 'pending')->count();
    }

    /**
     * Get pengaduan resolved
     */
    public function getPengaduanResolvedAttribute()
    {
        return $this->pengaduan()->where('status', 'resolved')->count();
    }
}