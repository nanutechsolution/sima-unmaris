<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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