<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Room;
use App\Models\Location;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $location = Location::first();

        if (!$location) {
            $this->command->warn('Location belum ada');
            return;
        }

        $rooms = [
            ['Ruang Rektor', 'RR-01'],
            ['Ruang Administrasi', 'ADM-01'],
            ['Lab Komputer A', 'LAB-01'],
            ['Lab Komputer B', 'LAB-02'],
            ['Ruang Dosen', 'DOS-01'],
            ['Perpustakaan', 'LIB-01'],
            ['Aula Kampus', 'AULA-01'],
        ];

        foreach ($rooms as $room) {
            Room::create([
                'id' => Str::uuid(),
                'location_id' => $location->id,
                'name' => $room[0],
                'code' => $room[1],
            ]);
        }
    }
}