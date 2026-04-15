<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\KategoriPengaduan;
use App\Models\Pengaduan;
use App\Models\TimelinePengaduan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ========================
        // USERS
        // ========================
        $admin = User::updateOrCreate(
            ['email' => 'admin@sekolah.ac.id'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'department' => 'TU',
                'is_active' => true,
            ]
        );

        $kepala = User::updateOrCreate(
            ['email' => 'kepala@sekolah.ac.id'],
            [
                'name' => 'Kepala Sekolah',
                'password' => Hash::make('password'),
                'role' => 'kepala_sekolah',
                'department' => 'Kurikulum',
                'is_active' => true,
            ]
        );

        $koordinator = User::updateOrCreate(
            ['email' => 'koordinator@sekolah.ac.id'],
            [
                'name' => 'Koordinator Sarpras',
                'password' => Hash::make('password'),
                'role' => 'koordinator',
                'department' => 'Sarpras',
                'is_active' => true,
            ]
        );

        $petugas1 = User::updateOrCreate(
            ['email' => 'petugas@sekolah.ac.id'],
            [
                'name' => 'Petugas Kebersihan',
                'password' => Hash::make('password'),
                'role' => 'petugas',
                'department' => 'Sarpras',
                'telp' => '081234567890',
                'is_active' => true,
            ]
        );

        $siswa = User::updateOrCreate(
            ['email' => 'siswa@sekolah.ac.id'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role' => 'user',
                'is_active' => true,
            ]
        );

        // ========================
        // KATEGORI PENGADUAN
        // ========================
        $kategoris = [
            [
                'nama' => 'Sarana Prasarana',
                'slug' => 'sarana-prasarana',
                'deskripsi' => 'Pengaduan terkait fasilitas dan sarana prasarana sekolah',
                'icon' => '🏫',
                'warna' => '#e5007d',
                'sla_hours' => 48,
                'pic_default_id' => $koordinator->id,
                'is_active' => true,
                'urutan' => 1,
            ],
            [
                'nama' => 'Kebersihan',
                'slug' => 'kebersihan',
                'deskripsi' => 'Pengaduan terkait kebersihan lingkungan sekolah',
                'icon' => '🧹',
                'warna' => '#22c55e',
                'sla_hours' => 24,
                'pic_default_id' => $petugas1->id,
                'is_active' => true,
                'urutan' => 2,
            ],
            [
                'nama' => 'Keamanan',
                'slug' => 'keamanan',
                'deskripsi' => 'Pengaduan terkait keamanan dan ketertiban sekolah',
                'icon' => '🛡️',
                'warna' => '#ef4444',
                'sla_hours' => 12,
                'pic_default_id' => $admin->id,
                'is_active' => true,
                'urutan' => 3,
            ],
            [
                'nama' => 'Akademik',
                'slug' => 'akademik',
                'deskripsi' => 'Pengaduan terkait proses belajar mengajar',
                'icon' => '📚',
                'warna' => '#3b82f6',
                'sla_hours' => 72,
                'pic_default_id' => $kepala->id,
                'is_active' => true,
                'urutan' => 4,
            ],
            [
                'nama' => 'Administrasi',
                'slug' => 'administrasi',
                'deskripsi' => 'Pengaduan terkait layanan administrasi dan tata usaha',
                'icon' => '📋',
                'warna' => '#a855f7',
                'sla_hours' => 48,
                'pic_default_id' => $admin->id,
                'is_active' => true,
                'urutan' => 5,
            ],
        ];

        foreach ($kategoris as $k) {
            KategoriPengaduan::updateOrCreate(['slug' => $k['slug']], $k);
        }

        $katSarpras = KategoriPengaduan::where('slug', 'sarana-prasarana')->first();
        $katKebersihan = KategoriPengaduan::where('slug', 'kebersihan')->first();
        $katKeamanan = KategoriPengaduan::where('slug', 'keamanan')->first();

        // ========================
        // PENGADUAN CONTOH
        // ========================
        $pengaduanData = [
            [
                'kategori_id' => $katSarpras->id,
                'judul' => 'AC Kelas XI RPL 1 Rusak',
                'deskripsi' => 'AC di kelas XI RPL 1 sudah tidak berfungsi selama 2 minggu. Suhu ruangan sangat panas sehingga mengganggu proses pembelajaran.',
                'tingkat_urgensi' => 'tinggi',
                'status' => 'in_progress',
                'is_anonim' => false,
                'user_id' => $siswa->id,
                'nama_pelapor' => $siswa->name,
                'email_pelapor' => $siswa->email,
                'lokasi_kejadian' => 'Kelas XI RPL 1, Lantai 2',
                'assigned_to' => $petugas1->id,
            ],
            [
                'kategori_id' => $katKebersihan->id,
                'judul' => 'WC Lantai 3 Tidak Bersih',
                'deskripsi' => 'Toilet di lantai 3 sebelah barat sudah beberapa hari tidak dibersihkan. Bau sangat menyengat dan mengganggu aktivitas siswa.',
                'tingkat_urgensi' => 'sedang',
                'status' => 'pending',
                'is_anonim' => true,
                'user_id' => null,
                'lokasi_kejadian' => 'Toilet Lantai 3 Barat',
            ],
            [
                'kategori_id' => $katSarpras->id,
                'judul' => 'Proyektor Lab Komputer Mati',
                'deskripsi' => 'Proyektor di Lab Komputer 2 tidak dapat dinyalakan. Sudah dicoba beberapa kali namun tidak ada respon sama sekali.',
                'tingkat_urgensi' => 'tinggi',
                'status' => 'resolved',
                'is_anonim' => false,
                'user_id' => $siswa->id,
                'nama_pelapor' => $siswa->name,
                'email_pelapor' => $siswa->email,
                'lokasi_kejadian' => 'Lab Komputer 2',
                'assigned_to' => $koordinator->id,
                'solusi' => 'Proyektor telah diperbaiki oleh teknisi. Kerusakan ada pada kabel power yang sudah aus. Kabel telah diganti dengan yang baru.',
                'resolved_at' => now()->subDays(3),
                'verified_at' => now()->subDays(5),
            ],
            [
                'kategori_id' => $katKeamanan->id,
                'judul' => 'Motor Siswa Hilang di Parkir',
                'deskripsi' => 'Motor saya hilang dari area parkir sekolah. Kejadian terjadi sekitar pukul 10.00 WIB. Mohon untuk ditindaklanjuti dan ditingkatkan keamanan area parkir.',
                'tingkat_urgensi' => 'darurat',
                'status' => 'verified',
                'is_anonim' => false,
                'user_id' => $siswa->id,
                'nama_pelapor' => 'Anonim (atas permintaan)',
                'email_pelapor' => $siswa->email,
                'lokasi_kejadian' => 'Area Parkir Motor Siswa',
                'assigned_to' => $admin->id,
            ],
            [
                'kategori_id' => $katKebersihan->id,
                'judul' => 'Sampah Menumpuk di Kantin',
                'deskripsi' => 'Tumpukan sampah di area kantin belum diangkut sejak beberapa hari. Menimbulkan bau tidak sedap dan berpotensi menjadi sarang nyamuk.',
                'tingkat_urgensi' => 'rendah',
                'status' => 'pending',
                'is_anonim' => true,
                'user_id' => null,
                'lokasi_kejadian' => 'Area Kantin Sekolah',
            ],
        ];

        foreach ($pengaduanData as $data) {
            $kode = Pengaduan::generateKodeUnik();
            $pengaduan = Pengaduan::create(array_merge($data, ['kode_unik' => $kode]));

            // Add timeline entries
            TimelinePengaduan::create([
                'pengaduan_id' => $pengaduan->id,
                'status' => 'pending',
                'catatan' => 'Pengaduan dibuat',
                'updated_by' => $data['user_id'] ?? $admin->id,
            ]);

            if (in_array($data['status'], ['verified', 'in_progress', 'resolved'])) {
                TimelinePengaduan::create([
                    'pengaduan_id' => $pengaduan->id,
                    'status' => 'verified',
                    'catatan' => 'Pengaduan telah diverifikasi oleh admin',
                    'updated_by' => $admin->id,
                    'created_at' => now()->subDays(2),
                ]);
            }

            if (in_array($data['status'], ['in_progress', 'resolved'])) {
                TimelinePengaduan::create([
                    'pengaduan_id' => $pengaduan->id,
                    'status' => 'in_progress',
                    'catatan' => 'Sedang ditangani oleh petugas',
                    'updated_by' => $data['assigned_to'] ?? $admin->id,
                    'created_at' => now()->subDays(1),
                ]);
            }

            if ($data['status'] === 'resolved') {
                TimelinePengaduan::create([
                    'pengaduan_id' => $pengaduan->id,
                    'status' => 'resolved',
                    'catatan' => 'Pengaduan berhasil diselesaikan',
                    'updated_by' => $data['assigned_to'] ?? $admin->id,
                    'created_at' => now()->subDays(0),
                ]);
            }
        }

        $this->command->info('✅ Seeder berhasil dijalankan!');
        $this->command->info('👤 Admin: admin@sekolah.ac.id / password');
        $this->command->info('👤 Kepala Sekolah: kepala@sekolah.ac.id / password');
        $this->command->info('👤 Petugas: petugas@sekolah.ac.id / password');
        $this->command->info('👤 Siswa: siswa@sekolah.ac.id / password');
    }
}