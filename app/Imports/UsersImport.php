<?php

namespace App\Imports;

use App\Models\User;
use App\Helpers\AnggotaHelper;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;
    
    private $successCount = 0;
    private $skippedCount = 0;
    private $errors = [];

    public function model(array $row)
    {
        // Mapping kolom dari template CSV
        $nisn_nik = trim($row['nisn_nik'] ?? '');
        $name = trim($row['name'] ?? '');
        $email = trim($row['email'] ?? '');
        $role = strtolower(trim($row['role'] ?? 'siswa'));
        $kelas = trim($row['kelas'] ?? '');
        $phone = trim($row['phone'] ?? '');
        $address = trim($row['address'] ?? '');
        
        // Validasi data wajib
        if (empty($nisn_nik)) {
            $this->skippedCount++;
            $this->errors[] = "NISN/NIK kosong";
            return null;
        }
        
        if (empty($name)) {
            $this->skippedCount++;
            $this->errors[] = "Nama kosong untuk NISN: {$nisn_nik}";
            return null;
        }
        
        // Validasi role
        $validRoles = ['siswa', 'guru', 'pegawai', 'umum'];
        if (!in_array($role, $validRoles)) {
            $role = 'siswa'; // default ke siswa
        }
        
        // Cek duplikat NISN/NIK
        $existingUser = User::where('nisn_nik', $nisn_nik)->first();
        if ($existingUser) {
            $this->skippedCount++;
            $this->errors[] = "NISN/NIK {$nisn_nik} sudah terdaftar";
            return null;
        }
        
        // Generate email jika kosong
        if (empty($email)) {
            $emailBase = strtolower(preg_replace('/[^a-z0-9]/i', '', $name));
            $email = $emailBase . '@anggota.sman1.sch.id';
        }
        
        // Pastikan email unik
        $originalEmail = $email;
        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $parts = explode('@', $originalEmail);
            $email = $parts[0] . $counter . '@' . ($parts[1] ?? 'anggota.sman1.sch.id');
            $counter++;
        }
        
        // Generate No Anggota
        $noAnggota = AnggotaHelper::generateNoAnggota($role);
        
        // Data user
        $userData = [
            'nisn_nik' => $nisn_nik,
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($nisn_nik), // password = NISN/NIK
            'role' => $role,
            'jenis' => $role,
            'no_anggota' => $noAnggota,
            'kelas' => !empty($kelas) ? $kelas : null,
            'phone' => !empty($phone) ? $phone : null,
            'address' => !empty($address) ? $address : null,
            'status' => 'active',
            'status_anggota' => 'active',
            'force_password_change' => true,
            'tanggal_daftar' => now(),
            'masa_berlaku' => now()->addYears(3),
            'approved_at' => now(),
            'approved_by' => Auth::id() ?? 1,
        ];
        
        $this->successCount++;
        Log::info("Import user: {$name} ({$nisn_nik}) - No Anggota: {$noAnggota}");
        
        return new User($userData);
    }

    public function rules(): array
    {
        return [
            'nisn_nik' => 'required',
            'name' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nisn_nik.required' => 'NISN/NIK harus diisi',
            'name.required' => 'Nama harus diisi',
        ];
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }
    
    public function getErrors(): array
    {
        return $this->errors;
    }
}