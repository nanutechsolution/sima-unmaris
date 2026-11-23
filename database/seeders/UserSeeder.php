<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'id' => Str::uuid(),
            'name' => 'Admin Aset',
            'email' => 'admin@unmaris.ac.id',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'id' => Str::uuid(),
            'name' => 'Petugas IT',
            'email' => 'it@unmaris.ac.id',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'id' => Str::uuid(),
            'name' => 'Kepala Lab',
            'email' => 'lab@unmaris.ac.id',
            'password' => Hash::make('password'),
        ]);
    }
}
