<?php

namespace Database\Seeders;

use App\Enums\AssetConditionEnum;
use App\Enums\AssetStatusEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Room;
use App\Models\Supplier;
use App\Models\User;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::first();
        $room = Room::first();
        $supplier = Supplier::first();
        $user = User::first();

        if (!$category || !$room || !$user) {
            $this->command->warn('Master data belum ada (Category, Room, User)');
            return;
        }

        $assets = [
            ['Laptop Asus ROG', 18000000],
            ['Laptop Lenovo Thinkpad', 15000000],
            ['Printer Epson L3210', 2500000],
            ['Proyektor Epson EB-X500', 8500000],
            ['PC Laboratorium Core i7', 12000000],
            ['Kamera Canon EOS M50', 9500000],
            ['Sound System Aula', 7000000],
            ['TV LED Samsung 55"', 9000000],
            ['Router Mikrotik RB4011', 4500000],
            ['Scanner Canon Lide 300', 1800000],
        ];

        $counter = 1;

        foreach ($assets as $asset) {

            Asset::create([
                'id' => Str::uuid(),
                'asset_code' => 'UNMARIS-' . $category->prefix_code . '-2026-' . str_pad($counter, 3, '0', STR_PAD_LEFT),
                'name' => $asset[0],
                'category_id' => $category->id,
                'room_id' => $room->id,
                'supplier_id' => $supplier?->id,
                'pic_user_id' => $user->id,
                'acquisition_value' => $asset[1],
                'acquisition_date' => now()->subDays(rand(10, 300)),
                'status' =>  AssetStatusEnum::AVAILABLE,
                'condition' => AssetConditionEnum::GOOD,
                'qr_signature_hash' => hash('sha256', Str::uuid()),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $counter++;
        }
    }
}
