<?php

namespace Database\Seeders;

use App\Models\KategoriPengaduan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KategoriPengaduanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'nama' => 'Kerusakan Fasilitas',
                'deskripsi' => 'Pengaduan terkait kerusakan meja, kursi, AC, lampu, atau gedung sekolah.',
                'icon' => 'fas fa-tools',
                'warna' => '#f44336',
                'sla_hours' => 48,
                'urutan' => 1,
            ],
            [
                'nama' => 'Kebersihan Lingkungan',
                'deskripsi' => 'Pengaduan terkait toilet kotor, sampah menumpuk, atau area yang kurang bersih.',
                'icon' => 'fas fa-broom',
                'warna' => '#4caf50',
                'sla_hours' => 24,
                'urutan' => 2,
            ],
            [
                'nama' => 'Keamanan & Ketertiban',
                'deskripsi' => 'Pengaduan terkait kehilangan, perundungan (bullying), atau gangguan keamanan.',
                'icon' => 'fas fa-shield-alt',
                'warna' => '#3f51b5',
                'sla_hours' => 12,
                'urutan' => 3,
            ],
            [
                'nama' => 'Layanan Administrasi',
                'deskripsi' => 'Pengaduan terkait kelambatan layanan TU, surat-menyurat, atau administrasi lainnya.',
                'icon' => 'fas fa-file-invoice',
                'warna' => '#ff9800',
                'sla_hours' => 48,
                'urutan' => 4,
            ],
            [
                'nama' => 'Kegiatan Belajar Mengajar',
                'deskripsi' => 'Pengaduan terkait jadwal pelajaran, cara mengajar guru, atau fasilitas lab.',
                'icon' => 'fas fa-chalkboard-teacher',
                'warna' => '#9c27b0',
                'sla_hours' => 72,
                'urutan' => 5,
            ],
            [
                'nama' => 'Lain-lain',
                'deskripsi' => 'Pengaduan yang tidak masuk dalam kategori yang sudah disediakan.',
                'icon' => 'fas fa-ellipsis-h',
                'warna' => '#607d8b',
                'sla_hours' => 96,
                'urutan' => 6,
            ],
        ];

        foreach ($categories as $category) {
            KategoriPengaduan::updateOrCreate(
                ['nama' => $category['nama']],
                array_merge($category, [
                    'slug' => Str::slug($category['nama']),
                    'is_active' => true,
                ])
            );
        }
    }
}
