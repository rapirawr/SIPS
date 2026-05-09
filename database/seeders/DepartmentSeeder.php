<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Sarana & Prasarana',
                'slug' => 'sarpras',
                'description' => 'Bertanggung jawab atas fasilitas dan infrastruktur sekolah.',
                'is_active' => true,
            ],
            [
                'name' => 'Tata Usaha',
                'slug' => 'tu',
                'description' => 'Mengelola administrasi dan kearsipan sekolah.',
                'is_active' => true,
            ],
            [
                'name' => 'Bimbingan Konseling',
                'slug' => 'bk',
                'description' => 'Menangani masalah kedisiplinan dan kesejahteraan siswa.',
                'is_active' => true,
            ],
            [
                'name' => 'Kurikulum',
                'slug' => 'kurikulum',
                'description' => 'Mengelola kegiatan belajar mengajar dan akademik.',
                'is_active' => true,
            ],
            [
                'name' => 'Kesiswaan',
                'slug' => 'kesiswaan',
                'description' => 'Mengelola organisasi siswa dan ekstrakurikuler.',
                'is_active' => true,
            ],
            [
                'name' => 'Keamanan',
                'slug' => 'security',
                'description' => 'Menjaga keamanan dan ketertiban lingkungan sekolah.',
                'is_active' => true,
            ],
            [
                'name' => 'Kebersihan',
                'slug' => 'cleaning',
                'description' => 'Menjaga kebersihan dan sanitasi sekolah.',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(['slug' => $dept['slug']], $dept);
        }
    }
}
