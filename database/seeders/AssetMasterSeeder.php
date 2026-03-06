<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\Room;
use App\Models\Supplier;
use App\Models\User;
use App\Enums\AssetStatusEnum;
use App\Enums\AssetConditionEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AssetMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. BUAT KATEGORI ASET
        $categories = [
            ['name' => 'Elektronik & IT', 'prefix_code' => 'EIT', 'description' => 'Laptop, Komputer, Printer, dsb.'],
            ['name' => 'Furnitur Kantor', 'prefix_code' => 'FNT', 'description' => 'Meja, Kursi, Lemari.'],
            ['name' => 'Kendaraan Dinas', 'prefix_code' => 'KND', 'description' => 'Mobil dan Motor operasional kampus.'],
            ['name' => 'Peralatan Medis', 'prefix_code' => 'MED', 'description' => 'Alat kesehatan poliklinik kampus.'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['prefix_code' => $cat['prefix_code']], $cat);
        }

        // 2. BUAT LOKASI & RUANGAN
        $locations = [
            [
                'name' => 'Kampus Pusat UNMARIS',
                'address' => 'Jl. Pendidikan No. 45, Sumba Barat',
                'rooms' => [
                    ['name' => 'Laboratorium Komputer A', 'code' => 'LAB-A'],
                    ['name' => 'Perpustakaan Utama', 'code' => 'LIB-01'],
                    ['name' => 'Ruang Rektorat', 'code' => 'RECT-01'],
                ]
            ],
            [
                'name' => 'Gedung Pascasarjana',
                'address' => 'Jl. Merdeka No. 10, Sumba Barat',
                'rooms' => [
                    ['name' => 'Aula Besar', 'code' => 'AULA-01'],
                    ['name' => 'Ruang Dosen PPs', 'code' => 'DSN-PPS'],
                ]
            ],
        ];

        foreach ($locations as $locData) {
            $location = Location::firstOrCreate(['name' => $locData['name']], [
                'address' => $locData['address']
            ]);

            foreach ($locData['rooms'] as $roomData) {
                Room::firstOrCreate(['code' => $roomData['code']], [
                    'location_id' => $location->id,
                    'name' => $roomData['name']
                ]);
            }
        }

        // 3. BUAT SUPPLIER / VENDOR
        $suppliers = [
            ['name' => 'PT. Global Teknologi', 'contact_person' => 'Andi Wijaya', 'phone' => '08123456789', 'address' => 'Jakarta IT Hub'],
            ['name' => 'CV. Furnitur Sejahtera', 'contact_person' => 'Siti Aminah', 'phone' => '08776655443', 'address' => 'Kawasan Industri Mebel'],
            ['name' => 'Sentra Motor Utama', 'contact_person' => 'Budi Otomotif', 'phone' => '08521122334', 'address' => 'Showroom Pusat Kota'],
        ];

        foreach ($suppliers as $sup) {
            Supplier::firstOrCreate(['name' => $sup['name']], $sup);
        }

        // 4. BUAT BEBERAPA ASET SAMPEL
        // Ambil data pendukung secara acak
        $categoryIT = Category::where('prefix_code', 'EIT')->first();
        $categoryFnt = Category::where('prefix_code', 'FNT')->first();
        $roomLab = Room::where('code', 'LAB-A')->first();
        $roomRect = Room::where('code', 'RECT-01')->first();
        $supplierTech = Supplier::first();
        $picUser = User::first(); // Mengambil user admin yang dibuat di seeder sebelumnya

        if ($picUser && $categoryIT && $roomLab) {
            $assets = [
                [
                    'asset_code' => 'UNMARIS/IT/2026/001',
                    'name' => 'MacBook Pro M3 14-inch',
                    'category_id' => $categoryIT->id,
                    'room_id' => $roomLab->id,
                    'pic_user_id' => $picUser->id,
                    'supplier_id' => $supplierTech->id,
                    'acquisition_value' => 35000000,
                    'acquisition_date' => now()->subMonths(6),
                    'status' => AssetStatusEnum::IN_USE,
                    'condition' => AssetConditionEnum::GOOD,
                ],
                [
                    'asset_code' => 'UNMARIS/IT/2026/002',
                    'name' => 'Printer Epson L3210',
                    'category_id' => $categoryIT->id,
                    'room_id' => $roomRect->id,
                    'pic_user_id' => $picUser->id,
                    'supplier_id' => $supplierTech->id,
                    'acquisition_value' => 2500000,
                    'acquisition_date' => now()->subYear(),
                    'status' => AssetStatusEnum::AVAILABLE,
                    'condition' => AssetConditionEnum::FAIR,
                ],
                [
                    'asset_code' => 'UNMARIS/FNT/2026/001',
                    'name' => 'Kursi Kerja Ergonomis Informa',
                    'category_id' => $categoryFnt->id,
                    'room_id' => $roomRect->id,
                    'pic_user_id' => $picUser->id,
                    'acquisition_value' => 1500000,
                    'acquisition_date' => now()->subMonths(2),
                    'status' => AssetStatusEnum::IN_USE,
                    'condition' => AssetConditionEnum::GOOD,
                ],
            ];

            foreach ($assets as $assetData) {
                Asset::firstOrCreate(['asset_code' => $assetData['asset_code']], $assetData);
            }
        }

        $this->command->info('✅ Master Data Aset (Kategori, Lokasi, Ruangan, Supplier, Aset) berhasil di-seeding!');
    }
}