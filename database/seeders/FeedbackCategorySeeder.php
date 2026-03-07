<?php

namespace Database\Seeders;

use App\Models\FeedbackCategory;
use Illuminate\Database\Seeder;

class FeedbackCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar kategori survei yang relevan untuk operasional dan fasilitas kampus
        $categories = [
            [
                'name' => 'Jaringan WiFi & Internet',
                'description' => 'Kestabilan, kecepatan, dan jangkauan sinyal internet/WiFi di lingkungan kampus.',
            ],
            [
                'name' => 'Kebersihan Toilet & Gedung',
                'description' => 'Kenyamanan, ketersediaan air, dan kebersihan toilet, lorong, lobi, serta area umum.',
            ],
            [
                'name' => 'Kenyamanan Ruang Kelas',
                'description' => 'Suhu AC/Kipas, kondisi meja/kursi, penerangan lampu, dan fungsi proyektor/LCD.',
            ],
            [
                'name' => 'Fasilitas Laboratorium',
                'description' => 'Kelengkapan, kebersihan, dan fungsi alat-alat praktikum maupun komputer lab.',
            ],
            [
                'name' => 'Keamanan & Area Parkir',
                'description' => 'Kapasitas parkir kendaraan, keamanan helm/motor, dan kinerja petugas keamanan (Satpam).',
            ],
            [
                'name' => 'Perpustakaan & Ruang Baca',
                'description' => 'Kenyamanan ruang baca, kelengkapan koleksi, dan fasilitas penunjang di perpustakaan.',
            ],
            [
                'name' => 'Fasilitas Ibadah & Olahraga',
                'description' => 'Kondisi tempat ibadah (masjid/kapel) sarana prasarana lapangan olahraga kampus.',
            ],
            [
                'name' => 'Pelayanan Biro Sarpras',
                'description' => 'Kecepatan respon dan keramahan petugas saat menangani pelaporan kerusakan/peminjaman aset.',
            ],
        ];

        foreach ($categories as $category) {
            // Gunakan firstOrCreate agar tidak terjadi duplikasi data jika di-seed berulang kali
            FeedbackCategory::firstOrCreate(
                ['name' => $category['name']], // Acuan pencarian
                ['description' => $category['description']] // Data yang diisi
            );
        }

        $this->command->info('✅ Kategori Survei Layanan Kampus berhasil di-seeding!');
    }
}
