<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PeminjamanDigital;
use App\Models\DigitalAccessLog;
use App\Models\Peminjaman;
use App\Models\PeminjamanLog;
use App\Models\Denda;
use App\Models\Kunjungan;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Notifikasi;
use App\Models\UlasanBuku;
use App\Models\FavoritBuku;
use App\Models\PoinAnggota;
use App\Models\BacaDiTempat;
use App\Models\StockOpnameLog;
use App\Models\ActivityLog;
use App\Models\Anggota;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\AnggotaHelper;

class KelolaAkunController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        $query->where(function($q) {
            $q->whereIn('role', ['admin', 'petugas', 'kepala_pustaka', 'pimpinan'])
            ->orWhere('status_anggota', 'active');
        });

        // 🔎 Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('nisn_nik', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // 🎭 Filter Role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // 📌 Filter Status (gunakan 'active'/'inactive')
        if ($request->filled('status')) {
            $status = $request->status == 'aktif' ? 'active' : 'inactive';
            $query->where('status', $status);
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('admin.pages.kelola-akun.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.kelola-akun.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nisn_nik' => 'required|string|unique:users,nisn_nik',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'role' => 'required|in:siswa,guru,pegawai,umum,petugas,admin,kepala_pustaka,pimpinan',
        ]);

        $noAnggota = null;

        DB::transaction(function() use ($request, &$noAnggota) {
            $data = [
                'nisn_nik' => $request->nisn_nik,
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->nisn_nik),
                'role' => $request->role,
                'status' => 'active',
                'force_password_change' => true,
            ];

            // Jika role adalah anggota (siswa, guru, pegawai, umum)
            if (in_array($request->role, ['siswa', 'guru', 'pegawai', 'umum'])) {
                $noAnggota = AnggotaHelper::generateNoAnggota($request->role);
                
                $data['jenis'] = $request->role;
                $data['no_anggota'] = $noAnggota;
                $data['status_anggota'] = 'active';
                $data['tanggal_daftar'] = now();
                $data['masa_berlaku'] = now()->addYear();
                $data['approved_at'] = now();
                $data['approved_by'] = Auth::id();
            }

            User::create($data);
        });

        return redirect()
            ->route('admin.kelola-akun.index')
            ->with('success', 'Akun berhasil dibuat' . ($noAnggota ? ' (No. Anggota: ' . $noAnggota . ')' : ''));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.pages.kelola-akun.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'nisn_nik' => 'required|unique:users,nisn_nik,' . $user->id,
            'name' => 'required',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'role' => 'required',
            'status' => 'required|in:active,inactive',
        ]);

        $updateData = $data;
        
        // Jika ada perubahan NISN_NIK, update password juga agar sinkron
        if ($user->nisn_nik != $data['nisn_nik']) {
            $updateData['password'] = Hash::make($data['nisn_nik']);
            $updateData['force_password_change'] = true;
        }
        
        // Jika status berubah menjadi inactive, tambahkan log
        if ($user->status != $data['status'] && $data['status'] == 'inactive') {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'update',
                'description' => 'Menonaktifkan akun ' . $user->name,
                'model' => 'User',
                'model_id' => $user->id,
                'ip_address' => $request->ip(),
                'role' => Auth::user()->role,
            ]);
        }

        $user->update($updateData);

        return redirect()->route('admin.kelola-akun.index')
            ->with('success', 'Akun berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Cegah hapus diri sendiri
        if ($user->id == Auth::id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }
        
        try {
            DB::beginTransaction();
            
            // Hapus data dari semua tabel yang memiliki foreign key ke users
            DigitalAccessLog::where('user_id', $user->id)->delete();
            PeminjamanDigital::where('user_id', $user->id)->delete();
            PeminjamanLog::where('user_id', $user->id)->delete();
            Peminjaman::where('user_id', $user->id)->delete();
            Denda::where('id_anggota', $user->id)->delete();
            Kunjungan::where('user_id', $user->id)->delete();
            Booking::where('user_id', $user->id)->delete();
            Notification::where('user_id', $user->id)->delete();
            Notifikasi::where('user_id', $user->id)->delete();
            UlasanBuku::where('user_id', $user->id)->delete();
            FavoritBuku::where('user_id', $user->id)->delete();
            PoinAnggota::where('user_id', $user->id)->delete();
            BacaDiTempat::where('anggota_id', $user->id)->delete();
            StockOpnameLog::where('user_id', $user->id)->delete();
            ActivityLog::where('user_id', $user->id)->delete();
            Anggota::where('user_id', $user->id)->delete();
            
            // Update referensi
            User::where('approved_by', $user->id)->update(['approved_by' => null]);
            User::where('processed_by', $user->id)->update(['processed_by' => null]);
            
            // Hapus user
            $user->delete();
            
            DB::commit();
            return back()->with('success', 'Akun berhasil dihapus.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting user ID ' . $id . ': ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus akun: ' . $e->getMessage());
        }
    }

    /**
     * Import users from Excel file - Enhanced version
     */
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $file = $request->file('excel_file');
            
            $import = new UsersImport;
            Excel::import($import, $file);
            
            $success = $import->getSuccessCount();
            $skipped = $import->getSkippedCount();
            $reasons = $import->getReasons();
            
            // Log hasil import
            Log::info("Import result: Success={$success}, Skipped={$skipped}");
            Log::info("Skip reasons: ", $reasons);
            
            if ($success > 0) {
                $message = "✅ Berhasil import {$success} data!";
                if ($skipped > 0) {
                    $message .= " ⚠️ {$skipped} data gagal.";
                    if (!empty($reasons)) {
                        $message .= " Contoh: " . implode(', ', array_slice($reasons, 0, 3));
                    }
                }
                return redirect()->route('admin.kelola-akun.index')->with('success', $message);
            } else {
                $errorMsg = "❌ Gagal import. Tidak ada data yang masuk.";
                if (!empty($reasons)) {
                    $errorMsg .= " Penyebab: " . implode(', ', array_slice($reasons, 0, 5));
                } else {
                    $errorMsg .= " Cek format file Excel. Header harus: nisn_nik, name, email, role, kelas, phone, address";
                }
                return redirect()->back()->with('error', $errorMsg);
            }
                    
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Reset password for a user.
     */
    public function resetPassword($id)
    {
        $user = User::findOrFail($id);

        // Reset password ke NISN/NIK
        $newPassword = $user->nisn_nik;

        $user->update([
            'password' => Hash::make($newPassword),
            'force_password_change' => true,
        ]);

        return back()->with('success', 'Password berhasil direset ke NISN/NIK.');
    }

    /**
     * Download template for import (CSV format - works for all users)
     */
    public function downloadTemplate()
    {
        $headers = ['nisn_nik', 'name', 'email', 'role', 'kelas', 'phone', 'address'];
        
        // Data contoh yang valid
        $sampleData = [
            ['12345678', 'Andi Wijaya', 'andi@perpustakaan.com', 'siswa', 'X IPA 1', '081234567890', 'Jl. Pendidikan No. 1'],
            ['87654321', 'Budi Santoso', 'budi@perpustakaan.com', 'guru', '', '081234567891', ''],
            ['11223344', 'Citra Dewi', 'citra@perpustakaan.com', 'pegawai', '', '081234567892', ''],
            ['44332211', 'Dewi Putri', 'dewi@perpustakaan.com', 'umum', '', '081234567893', ''],
            ['55667788', 'Eka Prasetya', 'eka@perpustakaan.com', 'siswa', 'XII IPA 2', '081234567894', 'Jl. Mawar No. 5'],
            ['99887766', 'Fajar Nugroho', 'fajar@perpustakaan.com', 'guru', '', '081234567895', ''],
        ];
        
        // Buat response CSV
        $callback = function() use ($headers, $sampleData) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 (fixes Excel opening issue)
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Write headers
            fputcsv($file, $headers);
            
            // Write sample data
            foreach ($sampleData as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return response()->streamDownload($callback, 'template_import_user.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_import_user.csv"',
        ]);
    }
}