<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Kampus Pusat Universitas Stella Maris Sumba',
                'address' => 'Jl. Mananga Aba, Kabupaten Sumba Barat',
            ],
            [
                'name' => 'Gedung Rektorat',
                'address' => 'Area Kampus Pusat',
            ],
            [
                'name' => 'Gedung Fakultas Teknologi Informasi',
                'address' => 'Area Kampus Pusat',
            ],
            [
                'name' => 'Gedung Laboratorium',
                'address' => 'Area Kampus Pusat',
            ],
            [
                'name' => 'Perpustakaan Kampus',
                'address' => 'Area Kampus Pusat',
            ],
        ];

        foreach ($locations as $location) {
            Location::create([
                'id' => Str::uuid(),
                'name' => $location['name'],
                'address' => $location['address'],
            ]);
        }
    }
}
