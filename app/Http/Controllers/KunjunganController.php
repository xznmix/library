<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kunjungan;
use App\Models\User;
use Carbon\Carbon;

class KunjunganController extends Controller
{
    /**
     * Tampilkan halaman absensi
     */
    public function index()
    {
        return view('kunjungan.index');
    }

    /**
     * Cari anggota berdasarkan NISN/NIK (AJAX)
     */
    public function cariAnggota(Request $request)
    {
        $nisn = $request->nisn;

        $user = User::where('nisn_nik', $nisn)
            ->whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])
            ->first();

        if (!$user) {
            return response()->json([
                'found' => false,
                'message' => 'NISN/NIK tidak ditemukan'
            ]);
        }

        // Hitung kunjungan hari ini untuk user ini
        $kunjunganHariIni = Kunjungan::where('user_id', $user->id)
            ->whereDate('tanggal', today())
            ->count();

        return response()->json([
            'found' => true,
            'data' => [
                'id' => $user->id,
                'nama' => $user->name,
                'nisn' => $user->nisn_nik,
                'jenis' => $user->role ?? 'umum',
                'kelas' => $user->kelas,
                'foto' => $user->foto_ktp ? asset('storage/'.$user->foto_ktp) : null,
                'kunjungan_ke' => $kunjunganHariIni + 1
            ]
        ]);
    }

    /**
     * Simpan kunjungan untuk anggota
     */
    public function storeAnggota(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id'
            ]);

            $user = User::find($request->user_id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ]);
            }

            $kunjungan = Kunjungan::create([
                'user_id' => $user->id,
                'nama' => $user->name,
                'jenis' => $user->role ?? 'umum',
                'kelas' => $user->kelas,
                'tanggal' => today(),
                'jam_masuk' => now()->format('H:i:s'),
            ]);

            // Hitung kunjungan keberapa hari ini
            $kunjunganKe = Kunjungan::where('user_id', $user->id)
                ->whereDate('tanggal', today())
                ->count();

            return response()->json([
                'success' => true,
                'message' => 'Selamat datang, ' . $user->name . '!',
                'data' => [
                    'nama' => $user->name,
                    'jam' => now()->format('H:i'),
                    'kunjungan_ke' => $kunjunganKe
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Simpan kunjungan untuk pemustaka baru
     */
    public function storePemustaka(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required|string|max:255',
                'jenis' => 'required|in:siswa,guru,pegawai,umum',
                'no_hp' => 'required|string|max:20',
            ]);

            // Hitung kunjungan ke berapa hari ini untuk nama yang sama
            $kunjunganKe = Kunjungan::where('nama', $request->nama)
                ->whereDate('tanggal', today())
                ->count() + 1;

            $kunjungan = Kunjungan::create([
                'nama' => $request->nama,
                'jenis' => $request->jenis,
                'no_hp' => $request->no_hp,
                'tanggal' => today(),
                'jam_masuk' => now()->format('H:i:s'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kunjungan berhasil dicatat!',
                'kunjungan_ke' => $kunjunganKe,
                'data' => [
                    'nama' => $request->nama,
                    'jam' => now()->format('H:i')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}