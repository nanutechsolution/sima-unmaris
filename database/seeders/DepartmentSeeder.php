<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil maksimal 5 user secara acak untuk dijadikan Kepala Unit / Dekan
        $users = User::inRandomOrder()->take(5)->get();

        $departments = [
            [
                'code' => 'FT',
                'name' => 'Fakultas Teknik',
                'description' => 'Membawahi prodi Teknik Informatika, Sistem Informasi, dan Teknik Sipil.',
            ],
            [
                'code' => 'FEB',
                'name' => 'Fakultas Ekonomi dan Bisnis',
                'description' => 'Membawahi prodi Manajemen dan Akuntansi.',
            ],
            [
                'code' => 'REK',
                'name' => 'Rektorat',
                'description' => 'Pusat administrasi pimpinan utama kampus.',
            ],
            [
                'code' => 'BAU',
                'name' => 'Biro Administrasi Umum',
                'description' => 'Mengurus logistik, inventaris fisik, dan fasilitas umum kampus.',
            ],
            [
                'code' => 'BTI',
                'name' => 'Biro Teknologi Informasi',
                'description' => 'Pusat pengelolaan infrastruktur IT, server, dan jaringan kampus.',
            ],
        ];

        foreach ($departments as $index => $dept) {
            // Pasangkan manager_id dengan user acak jika tersedia
            $managerId = isset($users[$index]) ? $users[$index]->id : null;

            Department::firstOrCreate(
                ['code' => $dept['code']], // Cek agar tidak duplikat berdasarkan kode
                [
                    'name' => $dept['name'],
                    'description' => $dept['description'],
                    'manager_id' => $managerId,
                ]
            );
        }

        $this->command->info('✅ Master Data Departemen/Unit Kerja berhasil di-seeding!');
    }
}