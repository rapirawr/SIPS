<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_unik', 20)->unique(); // PKS-20240101-ABC123
            $table->foreignId('kategori_id')->constrained('kategori_pengaduan')->cascadeOnDelete();
            $table->string('judul', 200);
            $table->text('deskripsi');
            
            // Tingkat Urgensi
            $table->enum('tingkat_urgensi', ['rendah', 'sedang', 'tinggi', 'darurat'])->default('sedang');
            
            // Status
            $table->enum('status', [
                'pending',      // Baru masuk
                'verified',     // Sudah diverifikasi
                'in_progress',  // Sedang diproses
                'resolved',     // Selesai
                'rejected'      // Ditolak
            ])->default('pending');
            
            // Informasi Pelapor
            $table->boolean('is_anonim')->default(false);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_pelapor', 100)->nullable();
            $table->string('email_pelapor', 100)->nullable();
            $table->string('telp_pelapor', 20)->nullable();
            
            // Detail Pengaduan
            $table->string('lokasi_kejadian', 200)->nullable();
            $table->date('tanggal_kejadian')->nullable();
            $table->json('bukti_foto')->nullable(); // Array of file paths
            
            // Assignment & Resolution
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan_internal')->nullable(); // Untuk admin/petugas
            $table->text('solusi')->nullable(); // Solusi yang diberikan
            $table->json('bukti_penyelesaian')->nullable(); // Array of file paths
            
            // Rating (opsional)
            $table->integer('rating')->nullable(); // 1-5
            $table->text('feedback')->nullable();
            
            // Timestamps
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes untuk performa
            $table->index('kode_unik');
            $table->index('status');
            $table->index('kategori_id');
            $table->index('user_id');
            $table->index('assigned_to');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduan');
    }
};