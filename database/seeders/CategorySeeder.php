<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Komputer & IT',
                'prefix_code' => 'IT',
                'description' => 'Laptop, PC, server, printer, jaringan',
            ],
            [
                'name' => 'Peralatan Laboratorium',
                'prefix_code' => 'LAB',
                'description' => 'Alat praktikum dan penelitian laboratorium',
            ],
            [
                'name' => 'Furniture',
                'prefix_code' => 'FUR',
                'description' => 'Meja, kursi, lemari, rak',
            ],
            [
                'name' => 'Peralatan Elektronik',
                'prefix_code' => 'ELK',
                'description' => 'Proyektor, sound system, TV',
            ],
            [
                'name' => 'Peralatan Kantor',
                'prefix_code' => 'ATK',
                'description' => 'Mesin fotocopy, scanner, alat kantor',
            ],
            [
                'name' => 'Kendaraan',
                'prefix_code' => 'KND',
                'description' => 'Mobil operasional dan motor kampus',
            ],
            [
                'name' => 'Peralatan Olahraga',
                'prefix_code' => 'OLR',
                'description' => 'Peralatan olahraga kampus',
            ],
            [
                'name' => 'Peralatan Kebersihan',
                'prefix_code' => 'KBR',
                'description' => 'Vacuum, alat pel, mesin kebersihan',
            ],
            [
                'name' => 'Peralatan Keamanan',
                'prefix_code' => 'SEC',
                'description' => 'CCTV, alarm, alat keamanan',
            ],
            [
                'name' => 'Peralatan Multimedia',
                'prefix_code' => 'MED',
                'description' => 'Kamera, lighting, alat produksi media',
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'id' => Str::uuid(),
                'name' => $category['name'],
                'prefix_code' => $category['prefix_code'],
                'description' => $category['description'],
            ]);
        }
    }
}
