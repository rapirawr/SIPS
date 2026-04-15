<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimelinePengaduan extends Model
{
    use HasFactory;

    protected $table = 'timeline_pengaduan';

    protected $fillable = [
        'pengaduan_id',
        'status',
        'catatan',
        'updated_by',
    ];

    /**
     * Relasi ke pengaduan
     */
    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class, 'pengaduan_id');
    }

    /**
     * Relasi ke user yang update
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Laporan Diterima',
            'verified' => 'Laporan Diverifikasi',
            'in_progress' => 'Sedang Diproses',
            'resolved' => 'Selesai Ditangani',
            'rejected' => 'Laporan Ditolak',
        ];

        return $labels[$this->status] ?? 'Status Tidak Dikenal';
    }
}