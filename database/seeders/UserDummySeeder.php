<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserDummySeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name' => 'Admin Perpustakaan',
                'email' => 'admin@perpustakaan.com',
                'password' => 'admin123',
                'role' => 'admin',
            ],
            [
                'name' => 'Petugas Perpustakaan',
                'email' => 'petugas@perpustakaan.com',
                'password' => 'petugas123',
                'role' => 'petugas',
            ],
            [
                'name' => 'Kepala Pustaka',
                'email' => 'kepala@perpustakaan.com',
                'password' => 'kepala123',
                'role' => 'kepala_pustaka',
            ],
            [
                'name' => 'Pimpinan Perpustakaan',
                'email' => 'pimpinan@perpustakaan.com',
                'password' => 'pimpinan123',
                'role' => 'pimpinan',
            ],
            [
                'name' => 'Siswa Contoh',
                'email' => 'siswa@perpustakaan.com',
                'password' => 'siswa123',
                'role' => 'siswa',
            ],
        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make($user['password']),
                'role' => $user['role'],
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
        }

        $this->command->info("\n✅ All users created successfully!\n");
        $this->command->info("====================================");
        foreach ($users as $user) {
            $this->command->info("{$user['email']} / {$user['password']}");
        }
        $this->command->info("====================================");
    }
}