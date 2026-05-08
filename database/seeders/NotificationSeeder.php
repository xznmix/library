<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])->get();
        
        foreach ($users as $user) {
            // Notifikasi jatuh tempo
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Buku Hampir Jatuh Tempo',
                'message' => 'Buku "Filosofi Teras" akan jatuh tempo dalam 2 hari. Segera kembalikan untuk menghindari denda.',
                'type' => 'warning',
                'link' => route('anggota.riwayat.index'),
                'is_read' => false
            ]);
            
            // Notifikasi peminjaman disetujui
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Peminjaman Disetujui',
                'message' => 'Buku "Pemrograman Web" telah disetujui. Silakan ambil buku di perpustakaan.',
                'type' => 'success',
                'link' => route('anggota.riwayat.index'),
                'is_read' => false
            ]);
        }
    }
}