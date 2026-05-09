<?php

namespace Database\Seeders;

use App\Models\Pengaduan;
use App\Models\KategoriPengaduan;
use App\Models\User;
use App\Models\TimelinePengaduan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PengaduanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bersihkan data lama agar tren terlihat bersih
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Pengaduan::truncate();
        TimelinePengaduan::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = KategoriPengaduan::all();
        $users = User::where('role', 'user')->get();
        $petugas = User::where('role', 'petugas')->get();
        $admin = User::where('role', 'admin')->first();
        
        if ($categories->isEmpty() || $users->isEmpty()) {
            $this->command->error('Kategori atau User belum tersedia. Jalankan KategoriPengaduanSeeder dan UserSeeder terlebih dahulu.');
            return;
        }

        $statuses = ['pending', 'verified', 'in_progress', 'resolved', 'rejected'];
        $urgencies = ['rendah', 'sedang', 'tinggi', 'darurat'];

        // Buat 150-200 pengaduan dalam 6 bulan terakhir untuk tren yang bagus
        $totalPengaduan = 200;
        
        for ($i = 0; $i < $totalPengaduan; $i++) {
            // Logika tren: buat lebih banyak data di bulan-bulan tertentu
            // Misalnya: lebih banyak pengaduan di bulan berjalan dan 2 bulan lalu
            $randDays = rand(0, 180);
            $createdAt = Carbon::now()->subDays($randDays)->subHours(rand(0, 23));
            
            // Randomly pick status based on age (older items more likely to be resolved)
            if ($randDays > 30) {
                $statusProb = rand(0, 10);
                if ($statusProb > 2) {
                    $status = 'resolved';
                } else {
                    $status = $statuses[array_rand($statuses)];
                }
            } else {
                $status = $statuses[array_rand($statuses)];
            }

            $urgency = $urgencies[array_rand($urgencies)];
            $category = $categories->random();
            $user = $users->random();

            $pengaduan = Pengaduan::create([
                'kode_unik' => 'PKS-' . $createdAt->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'kategori_id' => $category->id,
                'judul' => $this->getRandomTitle($category->nama),
                'deskripsi' => 'Ini adalah deskripsi simulasi pengaduan mengenai ' . $category->nama . '. Laporan ini dikirim untuk keperluan testing tren dashboard.',
                'tingkat_urgensi' => $urgency,
                'status' => $status,
                'is_anonim' => rand(0, 10) > 8,
                'user_id' => $user->id,
                'nama_pelapor' => $user->name,
                'email_pelapor' => $user->email,
                'lokasi_kejadian' => 'Area ' . ['Kelas X', 'Kantin', 'Lapangan', 'Toilet', 'Lab Komputer', 'Perpustakaan'][array_rand(['Kelas X', 'Kantin', 'Lapangan', 'Toilet', 'Lab Komputer', 'Perpustakaan'])],
                'tanggal_kejadian' => $createdAt->copy()->subDays(rand(0, 2))->format('Y-m-d'),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Tambahkan timeline
            $this->createTimeline($pengaduan, $createdAt, $status, $petugas, $admin);
        }

        $this->command->info("Berhasil membuat $totalPengaduan data pengaduan untuk tren dashboard.");
    }

    private function getRandomTitle($categoryName)
    {
        $map = [
            'Fasilitas' => ['Meja rusak di kelas', 'AC tidak dingin', 'Lampu mati', 'Pintu rusak'],
            'Kebersihan' => ['Sampah menumpuk', 'Toilet kotor', 'Genangan air', 'Bau tidak sedap'],
            'Keamanan' => ['Kehilangan barang', 'Ada siswa merokok', 'Bullying', 'Pagar rusak'],
            'Admin' => ['Antrian lama', 'Kesalahan data', 'Petugas tidak ramah', 'Surat belum selesai'],
            'Belajar' => ['Guru terlambat', 'Jadwal bentrok', 'Buku kurang', 'Proyektor rusak'],
            'Lain' => ['Usul tong sampah', 'Menu kantin', 'Suasana berisik', 'Request wifi'],
        ];

        foreach ($map as $keyword => $titles) {
            if (stripos($categoryName, $keyword) !== false) {
                return $titles[array_rand($titles)];
            }
        }

        return 'Laporan terkait ' . $categoryName;
    }

    private function createTimeline($pengaduan, $createdAt, $status, $petugasCollection, $admin)
    {
        $petugas = $petugasCollection->isNotEmpty() ? $petugasCollection->random() : $admin;

        // Pending
        TimelinePengaduan::create([
            'pengaduan_id' => $pengaduan->id,
            'status' => 'pending',
            'catatan' => 'Pengaduan telah masuk ke sistem.',
            'updated_by' => $pengaduan->user_id,
            'created_at' => $createdAt,
        ]);

        if ($status === 'pending') return;

        // Verified
        $verifiedAt = Carbon::parse($createdAt)->addHours(rand(1, 12));
        TimelinePengaduan::create([
            'pengaduan_id' => $pengaduan->id,
            'status' => 'verified',
            'catatan' => 'Admin telah memverifikasi laporan ini.',
            'updated_by' => $admin->id ?? 1,
            'created_at' => $verifiedAt,
        ]);
        
        $pengaduan->update(['verified_at' => $verifiedAt]);

        if ($status === 'verified') return;
        if ($status === 'rejected') {
            TimelinePengaduan::create([
                'pengaduan_id' => $pengaduan->id,
                'status' => 'rejected',
                'catatan' => 'Laporan ditolak karena data tidak valid.',
                'updated_by' => $admin->id ?? 1,
                'created_at' => $verifiedAt->addHour(),
            ]);
            return;
        }

        // In Progress
        $inProgressAt = $verifiedAt->copy()->addHours(rand(1, 24));
        TimelinePengaduan::create([
            'pengaduan_id' => $pengaduan->id,
            'status' => 'in_progress',
            'catatan' => 'Laporan sedang dalam penanganan petugas.',
            'updated_by' => $petugas->id ?? 1,
            'created_at' => $inProgressAt,
        ]);
        
        $pengaduan->update(['assigned_to' => $petugas->id ?? 1]);

        if ($status === 'in_progress') return;

        // Resolved
        $resolvedAt = $inProgressAt->copy()->addDays(rand(1, 3));
        TimelinePengaduan::create([
            'pengaduan_id' => $pengaduan->id,
            'status' => 'resolved',
            'catatan' => 'Masalah telah diselesaikan.',
            'updated_by' => $petugas->id ?? 1,
            'created_at' => $resolvedAt,
        ]);

        $pengaduan->update([
            'status' => 'resolved',
            'resolved_at' => $resolvedAt,
            'solusi' => 'Tindakan perbaikan telah selesai dilakukan oleh tim operasional.',
            'rating' => rand(3, 5),
            'feedback' => 'Sangat membantu, terima kasih.'
        ]);
    }
}
