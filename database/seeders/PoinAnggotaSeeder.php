<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PoinAnggota;
use App\Models\User;
use App\Models\Peminjaman;

class PoinAnggotaSeeder extends Seeder
{
    public function run(): void
    {
        // Beri poin untuk semua anggota berdasarkan riwayat peminjaman
        $users = User::where('role', 'anggota')->get();
        
        foreach ($users as $user) {
            $totalPinjam = Peminjaman::where('user_id', $user->id)->count();
            
            if ($totalPinjam > 0) {
                // Cek apakah sudah ada poin
                $sudahAda = PoinAnggota::where('user_id', $user->id)->exists();
                
                if (!$sudahAda) {
                    PoinAnggota::create([
                        'user_id' => $user->id,
                        'poin' => $totalPinjam * 10,
                        'keterangan' => 'Poin awal dari ' . $totalPinjam . ' kali peminjaman',
                        'jenis' => 'tambah',
                        'referensi' => null
                    ]);
                }
            }
        }
        
        $this->command->info('Poin anggota berhasil diinisialisasi!');
    }
}