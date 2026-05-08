<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AnggotaHelper
{
    /**
     * Generate nomor anggota otomatis
     * Format: [KODE_JENIS][TAHUN][BULAN][NOMOR_URUT]
     * Contoh: SIS25030001 (Siswa, Maret 2025, urutan 1)
     * 
     * @param string $jenis (siswa, guru, pegawai, umum)
     * @return string
     */
    public static function generateNoAnggota($jenis)
    {
        $kodeJenis = [
            'siswa' => 'SIS',
            'guru' => 'GRU',
            'pegawai' => 'PGW',
            'umum' => 'UMM'
        ][$jenis] ?? 'XX';
        
        $tahun = date('y');
        $bulan = date('m');
        $prefix = $kodeJenis . $tahun . $bulan;
        
        // Gunakan cache untuk mencegah duplicate dalam waktu bersamaan
        $cacheKey = "last_anggota_{$prefix}";
        
        // Cari nomor urut terakhir dari database
        $lastAnggota = User::where('no_anggota', 'like', $prefix . '%')
            ->orderBy('no_anggota', 'desc')
            ->first();
        
        $lastNumber = 0;
        if ($lastAnggota && $lastAnggota->no_anggota) {
            $lastNumber = intval(substr($lastAnggota->no_anggota, -4));
        }
        
        // Cek cache untuk nomor terbaru
        $cachedNumber = Cache::get($cacheKey, $lastNumber);
        $newNumber = $cachedNumber + 1;
        
        // Simpan ke cache untuk mencegah duplicate dalam satu request
        Cache::put($cacheKey, $newNumber, 60); // Cache 60 detik
        
        $noAnggota = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        
        return $noAnggota;
    }
    
    /**
     * Generate nomor anggota dengan format khusus
     * Format: [JENIS]/[TAHUN]/[BULAN]/[NOMOR_URUT]
     * Contoh: SISWA/2025/03/0001
     */
    public static function generateNoAnggotaAlternate($jenis)
    {
        $jenisUpper = strtoupper($jenis);
        $tahun = date('Y');
        $bulan = date('m');
        
        $prefix = $jenisUpper . '/' . $tahun . '/' . $bulan . '/';
        
        $lastAnggota = User::where('no_anggota', 'like', $prefix . '%')
            ->orderBy('no_anggota', 'desc')
            ->first();
        
        if ($lastAnggota && $lastAnggota->no_anggota) {
            $lastNumber = intval(substr($lastAnggota->no_anggota, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $newNumber;
    }
}