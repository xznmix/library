<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@sman1tambang.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Anda bisa tambahkan user lain untuk testing
        User::create([
            'name' => 'Petugas Perpustakaan',
            'email' => 'petugas@perpustakaan.sch.id',
            'password' => Hash::make('petugas123'),
            'role' => 'petugas',
            'status' => 'active',
        ]);
    }
}