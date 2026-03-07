<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            ['PT Teknologi Nusantara', 'Andi Saputra', '081234567890'],
            ['CV Sumber Komputer', 'Budi Santoso', '081298765432'],
            ['PT Digital Solusi', 'Maria Angel', '082112223333'],
            ['Toko Elektronik Sumba', 'Yosef Loda', '082134567890'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create([
                'id' => Str::uuid(),
                'name' => $supplier[0],
                'contact_person' => $supplier[1],
                'phone' => $supplier[2],
                'address' => 'Indonesia',
            ]);
        }
    }
}