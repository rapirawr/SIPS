<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $deptSarpras = Department::where('slug', 'sarpras')->first();
        $deptTU = Department::where('slug', 'tu')->first();
        $deptBK = Department::where('slug', 'bk')->first();

        $users = [
            [
                'name' => 'Admin Utama',
                'email' => 'admin@pks.sch.id',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'department' => 'TU', // Keep string column for compatibility if still used
                'is_active' => true,
            ],
            [
                'name' => 'Kepala Sekolah',
                'email' => 'kepsek@pks.sch.id',
                'password' => Hash::make('password'),
                'role' => 'kepala_sekolah',
                'department' => 'Kurikulum',
                'is_active' => true,
            ],
            [
                'name' => 'Koordinator Sarpras',
                'email' => 'koordinator@pks.sch.id',
                'password' => Hash::make('password'),
                'role' => 'koordinator',
                'department' => 'Sarpras',
                'department_id' => $deptSarpras->id ?? null,
                'is_active' => true,
            ],
            [
                'name' => 'Petugas Lapangan',
                'email' => 'petugas@pks.sch.id',
                'password' => Hash::make('password'),
                'role' => 'petugas',
                'department' => 'Sarpras',
                'department_id' => $deptSarpras->id ?? null,
                'is_active' => true,
            ],
            [
                'name' => 'Budi Siswa',
                'email' => 'siswa@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'is_active' => true,
            ],
            [
                'name' => 'Siti Siswi',
                'email' => 'siti@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(['email' => $userData['email']], $userData);
        }

        // Add more random users
        User::factory(10)->create([
            'role' => 'user',
            'is_active' => true,
        ]);
    }
}
