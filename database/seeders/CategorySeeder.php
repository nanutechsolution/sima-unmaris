<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Komputer & IT', 'prefix_code' => 'IT'],
            ['name' => 'Peralatan Laboratorium', 'prefix_code' => 'LAB'],
            ['name' => 'Furniture', 'prefix_code' => 'FUR'],
            ['name' => 'Peralatan Elektronik', 'prefix_code' => 'ELK'],
            ['name' => 'Peralatan Kantor', 'prefix_code' => 'ATK'],
            ['name' => 'Kendaraan', 'prefix_code' => 'KND'],
            ['name' => 'Peralatan Multimedia', 'prefix_code' => 'MED'],
            ['name' => 'Peralatan Keamanan', 'prefix_code' => 'SEC'],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create([
                'id' => \Illuminate\Support\Str::uuid(),
                'name' => $category['name'],
                'prefix_code' => $category['prefix_code'],
            ]);
        }
    }
}
