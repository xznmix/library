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
use App\Models\Anggota;  // ← Model Anggota (tabel: anggota)
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
            
            // PERBAIKAN: Hapus data dari semua tabel yang memiliki foreign key ke users
            
            // 1. Hapus data digital access logs (harus sebelum peminjaman digital)
            DigitalAccessLog::where('user_id', $user->id)->delete();
            
            // 2. Hapus data peminjaman digital
            PeminjamanDigital::where('user_id', $user->id)->delete();
            
            // 3. Hapus data peminjaman logs (harus sebelum peminjaman)
            PeminjamanLog::where('user_id', $user->id)->delete();
            
            // 4. Hapus data peminjaman buku
            Peminjaman::where('user_id', $user->id)->delete();
            
            // 5. Hapus data denda
            Denda::where('id_anggota', $user->id)->delete();
            
            // 6. Hapus data kunjungan
            Kunjungan::where('user_id', $user->id)->delete();
            
            // 7. Hapus data booking
            Booking::where('user_id', $user->id)->delete();
            
            // 8. Hapus data notifikasi (dua tabel)
            Notification::where('user_id', $user->id)->delete();
            Notifikasi::where('user_id', $user->id)->delete();
            
            // 9. Hapus data ulasan/rating
            UlasanBuku::where('user_id', $user->id)->delete();
            
            // 10. Hapus data favorit buku
            FavoritBuku::where('user_id', $user->id)->delete();
            
            // 11. Hapus data poin anggota
            PoinAnggota::where('user_id', $user->id)->delete();
            
            // 12. Hapus data baca di tempat
            BacaDiTempat::where('anggota_id', $user->id)->delete();
            
            // 13. Hapus data stock opname logs
            StockOpnameLog::where('user_id', $user->id)->delete();
            
            // 14. Hapus data activity logs
            ActivityLog::where('user_id', $user->id)->delete();
            
            // 15. Hapus data anggota - PERBAIKAN: Gunakan tabel 'anggota' bukan 'anggotas'
            // Model Anggota menggunakan tabel 'anggota'
            Anggota::where('user_id', $user->id)->delete();
            
            // 16. Update referensi approved_by, processed_by, dll menjadi null
            User::where('approved_by', $user->id)->update(['approved_by' => null]);
            User::where('processed_by', $user->id)->update(['processed_by' => null]);
            
            // 17. Hapus user
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
            $import = new UsersImport;
            Excel::import($import, $request->file('excel_file'));
            
            $imported = $import->getRowCount();
            $skipped = $import->getSkippedCount();
            
            if ($imported > 0) {
                $message = "✅ {$imported} data berhasil diimport!";
                
                if ($skipped > 0) {
                    $message .= "<br>⚠️ {$skipped} data dilewati (NISN/NIK sudah ada atau data tidak valid).";
                }
                
                return redirect()
                    ->route('admin.kelola-akun.index')
                    ->with('success', $message);
            } else {
                return redirect()
                    ->back()
                    ->with('error', 'Tidak ada data baru yang diimport. Periksa format file Excel Anda.');
            }
                    
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }
            return redirect()->back()->with('error', 'Validasi gagal:<br>' . implode('<br>', array_slice($errorMessages, 0, 10)));
                    
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
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
     * Download template for import (dengan contoh data real dari database)
     */
    public function downloadTemplate()
    {
        $headers = ['nisn_nik', 'name', 'email', 'role', 'no_anggota', 'kelas', 'phone'];
        
        // Ambil beberapa data real dari database sebagai contoh
        $sampleUsers = User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])
            ->limit(5)
            ->get()
            ->map(function($user) {
                return [
                    $user->nisn_nik,
                    $user->name,
                    $user->email,
                    $user->role,
                    $user->no_anggota ?? '',
                    $user->kelas ?? '',
                    $user->phone ?? '',
                ];
            });
        
        // Jika tidak ada data, pakai contoh default
        if ($sampleUsers->isEmpty()) {
            $sampleUsers = collect([
                ['12345678', 'Andi Wijaya', 'andi@email.com', 'siswa', 'SIS2401001', 'X IPA 1', '08123456789'],
                ['87654321', 'Budi Santoso', 'budi@email.com', 'guru', 'GRU2401001', '', '08123456788'],
            ]);
        }
        
        $tempFile = tempnam(sys_get_temp_dir(), 'template_') . '.csv';
        $handle = fopen($tempFile, 'w');
        
        // Write headers
        fputcsv($handle, $headers);
        
        // Write sample data
        foreach ($sampleUsers as $row) {
            fputcsv($handle, $row);
        }
        
        fclose($handle);
        
        return response()->download($tempFile, 'template_import_user.csv')->deleteFileAfterSend(true);
    }
}