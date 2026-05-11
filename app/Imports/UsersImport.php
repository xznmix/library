<?php

namespace App\Imports;

use App\Models\User;
use App\Helpers\AnggotaHelper;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UsersImport implements ToModel, WithHeadingRow
{
    private $successCount = 0;
    private $skippedCount = 0;
    private $reason = [];

    public function model(array $row)
    {
        // LOG: Lihat data yang diterima dari Excel
        Log::info('Import row:', $row);
        
        // Cari kolom yang benar (case insensitive)
        $nisn_nik = $row['nisn_nik'] ?? $row['NISN_NIK'] ?? $row['NISN/NIK'] ?? null;
        $name = $row['name'] ?? $row['NAME'] ?? $row['Nama'] ?? $row['NAMA'] ?? null;
        $email = $row['email'] ?? $row['EMAIL'] ?? null;
        $role = $row['role'] ?? $row['ROLE'] ?? $row['Jenis Anggota'] ?? null;
        $kelas = $row['kelas'] ?? $row['KELAS'] ?? null;
        $phone = $row['phone'] ?? $row['PHONE'] ?? $row['No HP'] ?? null;
        $address = $row['address'] ?? $row['ADDRESS'] ?? $row['Alamat'] ?? null;
        
        // Cek 1: NISN/NIK kosong
        if (empty($nisn_nik)) {
            $this->skippedCount++;
            $this->reason[] = "NISN/NIK kosong";
            Log::warning('Import skipped: NISN/NIK kosong', $row);
            return null;
        }
        
        // Cek 2: Nama kosong
        if (empty($name)) {
            $this->skippedCount++;
            $this->reason[] = "Nama kosong untuk NISN: {$nisn_nik}";
            Log::warning("Import skipped: Nama kosong untuk NISN {$nisn_nik}");
            return null;
        }
        
        // Cek 3: Duplikat NISN/NIK
        if (User::where('nisn_nik', $nisn_nik)->exists()) {
            $this->skippedCount++;
            $this->reason[] = "NISN/NIK {$nisn_nik} sudah ada";
            Log::warning("Import skipped: NISN {$nisn_nik} sudah terdaftar");
            return null;
        }
        
        // Cek 4: Role (default siswa)
        $validRoles = ['siswa', 'guru', 'pegawai', 'umum'];
        $role = strtolower(trim($role ?? 'siswa'));
        if (!in_array($role, $validRoles)) {
            $role = 'siswa';
            Log::info("Role diganti ke siswa untuk NISN {$nisn_nik}");
        }
        
        // Generate email jika kosong
        if (empty($email)) {
            $emailBase = strtolower(preg_replace('/[^a-z0-9]/i', '', $name));
            $email = $emailBase . '@siswa.sman1.sch.id';
        }
        
        // Pastikan email unik
        $originalEmail = $email;
        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $parts = explode('@', $originalEmail);
            $email = $parts[0] . $counter . '@' . ($parts[1] ?? 'siswa.sman1.sch.id');
            $counter++;
        }
        
        // Generate No Anggota
        $noAnggota = AnggotaHelper::generateNoAnggota($role);
        
        // BUAT USER
        try {
            $user = User::create([
                'nisn_nik' => (string) $nisn_nik,
                'name' => trim($name),
                'email' => $email,
                'password' => Hash::make((string) $nisn_nik),
                'role' => $role,
                'jenis' => $role,
                'no_anggota' => $noAnggota,
                'kelas' => !empty($kelas) ? trim($kelas) : null,
                'phone' => !empty($phone) ? trim($phone) : null,
                'address' => !empty($address) ? trim($address) : null,
                'status' => 'active',
                'status_anggota' => 'active',
                'force_password_change' => true,
                'tanggal_daftar' => now(),
                'masa_berlaku' => now()->addYears(3),
                'approved_at' => now(),
                'approved_by' => Auth::id() ?? 1,
            ]);
            
            $this->successCount++;
            Log::info("✅ Import SUCCESS: {$name} ({$nisn_nik}) -> No Anggota: {$noAnggota}");
            
            return $user;
            
        } catch (\Exception $e) {
            $this->skippedCount++;
            $this->reason[] = "Error create user: " . $e->getMessage();
            Log::error("Import failed create user: " . $e->getMessage());
            return null;
        }
    }
    
    public function getSuccessCount(): int
    {
        return $this->successCount;
    }
    
    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }
    
    public function getReasons(): array
    {
        return $this->reason;
    }
}