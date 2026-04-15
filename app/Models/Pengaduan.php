<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Pengaduan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pengaduan';

    protected $fillable = [
        'kode_unik',
        'kategori_id',
        'judul',
        'deskripsi',
        'tingkat_urgensi',
        'status',
        'is_anonim',
        'user_id',
        'nama_pelapor',
        'email_pelapor',
        'telp_pelapor',
        'lokasi_kejadian',
        'tanggal_kejadian',
        'bukti_foto',
        'assigned_to',
        'catatan_internal',
        'solusi',
        'bukti_penyelesaian',
        'rating',
        'feedback',
        'verified_at',
        'resolved_at',
    ];

    protected $casts = [
        'is_anonim' => 'boolean',
        'bukti_foto' => 'array',
        'bukti_penyelesaian' => 'array',
        'tanggal_kejadian' => 'date',
        'verified_at' => 'datetime',
        'resolved_at' => 'datetime',
        'rating' => 'integer',
    ];

    /**
     * Boot function
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate kode unik saat create
        static::creating(function ($pengaduan) {
            if (empty($pengaduan->kode_unik)) {
                $pengaduan->kode_unik = static::generateKodeUnik();
            }
        });

        // Update timestamp saat status berubah
        static::updating(function ($pengaduan) {
            if ($pengaduan->isDirty('status')) {
                if ($pengaduan->status === 'verified' && empty($pengaduan->verified_at)) {
                    $pengaduan->verified_at = now();
                }
                if ($pengaduan->status === 'resolved' && empty($pengaduan->resolved_at)) {
                    $pengaduan->resolved_at = now();
                }
            }
        });
    }

    /**
     * Generate kode unik
     */
    public static function generateKodeUnik()
    {
        do {
            $kode = 'PKS-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6));
        } while (static::where('kode_unik', $kode)->exists());

        return $kode;
    }

    /**
     * RELATIONSHIPS
     */
    public function kategori()
    {
        return $this->belongsTo(KategoriPengaduan::class, 'kategori_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function timeline()
    {
        return $this->hasMany(TimelinePengaduan::class, 'pengaduan_id')->orderBy('created_at', 'desc');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'pengaduan_id');
    }

    /**
     * SCOPES
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeUrgent($query)
    {
        return $query->where('tingkat_urgensi', 'darurat');
    }

    public function scopeAnonim($query)
    {
        return $query->where('is_anonim', true);
    }

    public function scopeKategori($query, $kategoriId)
    {
        return $query->where('kategori_id', $kategoriId);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('kode_unik', 'like', "%{$search}%")
              ->orWhere('judul', 'like', "%{$search}%")
              ->orWhere('deskripsi', 'like', "%{$search}%")
              ->orWhere('nama_pelapor', 'like', "%{$search}%");
        });
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeOverdueSla($query)
    {
        return $query->whereHas('kategori', function ($q) {
            $q->whereRaw('TIMESTAMPDIFF(HOUR, pengaduan.created_at, NOW()) > kategori_pengaduan.sla_hours');
        })->whereNotIn('status', ['resolved', 'rejected']);
    }

    /**
     * ACCESSORS
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => ['text' => 'Pending', 'class' => 'bg-yellow-100 text-yellow-800'],
            'verified' => ['text' => 'Terverifikasi', 'class' => 'bg-blue-100 text-blue-800'],
            'in_progress' => ['text' => 'Diproses', 'class' => 'bg-purple-100 text-purple-800'],
            'resolved' => ['text' => 'Selesai', 'class' => 'bg-green-100 text-green-800'],
            'rejected' => ['text' => 'Ditolak', 'class' => 'bg-red-100 text-red-800'],
        ];

        return $badges[$this->status] ?? ['text' => 'Unknown', 'class' => 'bg-gray-100 text-gray-800'];
    }

    public function getUrgensiBadgeAttribute()
    {
        $badges = [
            'rendah' => ['text' => 'Rendah', 'class' => 'bg-gray-100 text-gray-800'],
            'sedang' => ['text' => 'Sedang', 'class' => 'bg-blue-100 text-blue-800'],
            'tinggi' => ['text' => 'Tinggi', 'class' => 'bg-orange-100 text-orange-800'],
            'darurat' => ['text' => 'Darurat', 'class' => 'bg-red-100 text-red-800'],
        ];

        return $badges[$this->tingkat_urgensi] ?? ['text' => 'Sedang', 'class' => 'bg-blue-100 text-blue-800'];
    }

    public function getDurasiPenangananAttribute()
    {
        if (!$this->resolved_at) {
            return null;
        }

        return $this->created_at->diffInHours($this->resolved_at);
    }

    public function getDurasiPenangananFormattedAttribute()
    {
        if (!$this->resolved_at) {
            return null;
        }

        $diff = $this->created_at->diff($this->resolved_at);
        
        $parts = [];
        if ($diff->d > 0) {
            $parts[] = $diff->d . ' Hari';
        }
        if ($diff->h > 0) {
            $parts[] = $diff->h . ' Jam';
        }
        if ($diff->i > 0 && $diff->d == 0) {
            $parts[] = $diff->i . ' Menit';
        }
        
        if (empty($parts)) {
            return 'Kurang dari 1 menit';
        }
        
        return implode(' ', $parts);
    }

    public function getIsOverdueSlaAttribute()
    {
        if (in_array($this->status, ['resolved', 'rejected'])) {
            return false;
        }

        $slaHours = $this->kategori->sla_hours ?? 48;
        $hoursElapsed = $this->created_at->diffInHours(now());

        return $hoursElapsed > $slaHours;
    }

    public function getSlaRemainingHoursAttribute()
    {
        if (in_array($this->status, ['resolved', 'rejected'])) {
            return 0;
        }

        $slaHours = $this->kategori->sla_hours ?? 48;
        $hoursElapsed = $this->created_at->diffInHours(now());

        return max(0, $slaHours - $hoursElapsed);
    }

    public function getSlaRemainingFormattedAttribute()
    {
        if (in_array($this->status, ['resolved', 'rejected'])) {
            return '-';
        }

        if ($this->is_overdue_sla) {
            $slaHours = $this->kategori->sla_hours ?? 48;
            $targetTime = $this->created_at->copy()->addHours($slaHours);
            $diff = now()->diff($targetTime);
            
            $text = 'Terlampaui (';
            if ($diff->d > 0) $text .= $diff->d . ' Hari ';
            if ($diff->h > 0) $text .= $diff->h . ' Jam';
            if ($diff->d == 0 && $diff->h == 0) $text .= $diff->i . ' Menit';
            
            return trim($text) . ')';
        }

        $slaHours = $this->kategori->sla_hours ?? 48;
        $targetTime = $this->created_at->copy()->addHours($slaHours);
        
        $diff = now()->diff($targetTime);
        
        $parts = [];
        if ($diff->d > 0) {
            $parts[] = $diff->d . ' Hari';
        }
        if ($diff->h > 0) {
            $parts[] = $diff->h . ' Jam';
        }
        if ($diff->i > 0 && $diff->d == 0) { // Only show mins if no days
            $parts[] = $diff->i . ' Menit';
        }
        
        if (empty($parts)) {
            return 'Kurang dari 1 menit';
        }
        
        return implode(' ', $parts);
    }

    /**
     * METHODS
     */
    public function updateStatus($newStatus, $catatan = null, $updatedBy = null)
    {
        $this->update(['status' => $newStatus]);

        // Tambah ke timeline
        TimelinePengaduan::create([
            'pengaduan_id' => $this->id,
            'status' => $newStatus,
            'catatan' => $catatan,
            'updated_by' => $updatedBy,
        ]);

        // Trigger notifikasi (akan dihandle oleh Event & Listener)
        event(new \App\Events\StatusPengaduanBerubah($this, $newStatus));

        return $this;
    }

    public function assignTo($userId, $catatan = null)
    {
        $this->update(['assigned_to' => $userId]);

        TimelinePengaduan::create([
            'pengaduan_id' => $this->id,
            'status' => $this->status,
            'catatan' => $catatan ?? "Ditugaskan ke " . $this->assignedUser->name,
            'updated_by' => auth()->id(),
        ]);

        return $this;
    }
}