<?php

namespace App\Imports;

use App\Models\User;
use App\Helpers\AnggotaHelper;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnFailure
{
    use SkipsFailures;
    
    private $rows = 0;
    private $skipped = 0;
    private $errors = [];
    
    // Mapping kolom Excel ke database
    private $columnMapping = [
        'nisn_nik' => ['NO. IDENTITAS*', 'NO_IDENTITAS', 'nisn_nik', 'NISN_NIK'],
        'name' => ['NAMA', 'NAME', 'nama', 'name'],
        'role' => ['JENIS ANGGOTA*', 'JENIS_ANGGOTA', 'role', 'ROLE'],
        'no_anggota' => ['NO ANGGOTA', 'NO_ANGGOTA', 'no_anggota', 'NO ANGGOTA', 'NO_ANGGOTA'],
        'kelas' => ['KELAS SISWA', 'KELAS_SISWA', 'kelas', 'KELAS'],
        'phone' => ['NO. HP', 'NO_HP', 'phone', 'PHONE', 'NO HP', 'NOMOR HP'],
        'address' => ['ALAMAT TEMPAT TINGGAL SEKARANG', 'ALAMAT', 'address', 'ADDRESS'],
        'jenis_kelamin' => ['JENIS KELAMIN*', 'JENIS_KELAMIN', 'jenis_kelamin', 'JENIS KELAMIN'],
        'tanggal_daftar' => ['TANGGAL PENDAFTARAN*', 'TANGGAL_PENDAFTARAN', 'tanggal_daftar', 'TANGGAL DAFTAR'],
        'masa_berlaku' => ['TANGGAL AKHIR BERLAKU*', 'TANGGAL_AKHIR_BERLAKU', 'masa_berlaku', 'TANGGAL AKHIR BERLAKU'],
        'status_anggota' => ['STATUS ANGGOTA', 'STATUS_ANGGOTA', 'status_anggota', 'STATUS ANGGOTA'],
    ];

    public function model(array $row)
    {
        $this->rows++;
        
        // Mapping data dari berbagai kemungkinan nama kolom
        $nisn_nik = $this->getValueFromRow($row, 'nisn_nik');
        $name = $this->getValueFromRow($row, 'name');
        $role = $this->getValueFromRow($row, 'role');
        $no_anggota = $this->getValueFromRow($row, 'no_anggota');
        $kelas = $this->getValueFromRow($row, 'kelas');
        $phone = $this->getValueFromRow($row, 'phone');
        $address = $this->getValueFromRow($row, 'address');
        $jenis_kelamin = $this->getValueFromRow($row, 'jenis_kelamin');
        $tanggal_daftar = $this->getValueFromRow($row, 'tanggal_daftar');
        $masa_berlaku = $this->getValueFromRow($row, 'masa_berlaku');
        $status_anggota = $this->getValueFromRow($row, 'status_anggota');
        
        // Validasi data wajib
        if (empty($nisn_nik)) {
            $this->skipped++;
            Log::warning("Baris {$this->rows}: NISN/NIK kosong, dilewati");
            return null;
        }
        
        if (empty($name)) {
            $this->skipped++;
            Log::warning("Baris {$this->rows}: Nama kosong, dilewati");
            return null;
        }
        
        // Cek duplikat NISN/NIK
        $exists = User::where('nisn_nik', $nisn_nik)->exists();
        if ($exists) {
            $this->skipped++;
            Log::warning("Baris {$this->rows}: NISN/NIK {$nisn_nik} sudah ada, dilewati");
            return null;
        }
        
        // Cek duplikat No Anggota (jika ada)
        if (!empty($no_anggota)) {
            $noAnggotaExists = User::where('no_anggota', $no_anggota)->exists();
            if ($noAnggotaExists) {
                $this->skipped++;
                Log::warning("Baris {$this->rows}: No Anggota {$no_anggota} sudah ada, dilewati");
                return null;
            }
        }
        
        // Tentukan role (default: siswa)
        $validRoles = ['siswa', 'guru', 'pegawai', 'umum', 'admin', 'petugas', 'kepala_pustaka', 'pimpinan'];
        $roleLower = strtolower(trim($role ?? 'siswa'));
        
        if (!in_array($roleLower, $validRoles)) {
            // Mapping dari data Excel "Pelajar" -> "siswa"
            if (str_contains(strtolower($role), 'pelajar') || str_contains(strtolower($role), 'siswa')) {
                $roleLower = 'siswa';
            } elseif (str_contains(strtolower($role), 'guru')) {
                $roleLower = 'guru';
            } elseif (str_contains(strtolower($role), 'pegawai')) {
                $roleLower = 'pegawai';
            } else {
                $roleLower = 'siswa';
            }
        }
        
        // 🔥 GENERATE EMAIL OTOMATIS dari nama (tanpa spasi, lowercase)
        $emailBase = strtolower(preg_replace('/[^a-z0-9]/i', '', $name));
        $email = $emailBase . '@anggota.sman1.sch.id';
        
        // Pastikan email unik
        $email = $this->makeUniqueEmail($email);
        
        // Password = NISN/NIK
        $password = (string) $nisn_nik;
        
        // 🔥 Tentukan No Anggota
        if (!empty($no_anggota)) {
            // Gunakan No Anggota dari Excel
            $finalNoAnggota = $no_anggota;
        } else {
            // Generate No Anggota otomatis
            $finalNoAnggota = $this->generateNoAnggota($roleLower);
        }
        
        // Format tanggal
        $tanggalDaftar = $this->parseDate($tanggal_daftar) ?? now();
        $masaBerlaku = $this->parseDate($masa_berlaku) ?? now()->addYears(3);
        
        // Status anggota
        $statusAnggota = 'active';
        if (!empty($status_anggota)) {
            $statusLower = strtolower(trim($status_anggota));
            if ($statusLower === 'aktif' || $statusLower === 'active') {
                $statusAnggota = 'active';
            } elseif ($statusLower === 'nonaktif' || $statusLower === 'inactive') {
                $statusAnggota = 'inactive';
            } else {
                $statusAnggota = 'active';
            }
        }
        
        // 🔥 Buat User Baru
        $userData = [
            'nisn_nik' => (string) $nisn_nik,
            'name' => trim($name),
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $roleLower,
            'jenis' => $roleLower,
            'no_anggota' => $finalNoAnggota,
            'kelas' => !empty($kelas) ? trim($kelas) : null,
            'phone' => !empty($phone) ? trim($phone) : null,
            'address' => !empty($address) ? trim($address) : null,
            'jenis' => !empty($jenis_kelamin) ? trim($jenis_kelamin) : null,
            'status' => 'active',
            'status_anggota' => $statusAnggota,
            'force_password_change' => true,
            'tanggal_daftar' => $tanggalDaftar,
            'masa_berlaku' => $masaBerlaku,
            'approved_at' => now(),
            'approved_by' => 1,
        ];
        
        // Hapus nilai null untuk menghindari error
        $userData = array_filter($userData, function($value) {
            return !is_null($value);
        });
        
        $user = new User($userData);
        
        Log::info("User created: NISN={$nisn_nik}, Name={$name}, Email={$email}, NoAnggota={$finalNoAnggota}, Role={$roleLower}");
        
        return $user;
    }
    
    /**
     * Ambil nilai dari row dengan berbagai kemungkinan nama kolom
     */
    private function getValueFromRow(array $row, string $field)
    {
        $possibleKeys = $this->columnMapping[$field] ?? [];
        
        foreach ($possibleKeys as $key) {
            // Coba dengan key asli
            if (isset($row[$key]) && !empty($row[$key])) {
                return $row[$key];
            }
            
            // Coba dengan key lowercase
            $lowerKey = strtolower($key);
            if (isset($row[$lowerKey]) && !empty($row[$lowerKey])) {
                return $row[$lowerKey];
            }
            
            // Coba dengan key tanpa spasi
            $noSpaceKey = str_replace(' ', '_', $key);
            if (isset($row[$noSpaceKey]) && !empty($row[$noSpaceKey])) {
                return $row[$noSpaceKey];
            }
        }
        
        // Fallback: coba cari key yang mirip
        foreach ($row as $key => $value) {
            if (!empty($value) && (
                str_contains(strtolower($key), strtolower($field)) ||
                str_contains(strtolower($key), str_replace('_', ' ', strtolower($field)))
            )) {
                return $value;
            }
        }
        
        return null;
    }
    
    /**
     * Parse tanggal dari berbagai format
     */
    private function parseDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }
        
        try {
            // Format: DD-MM-YYYY
            if (preg_match('/\d{2}-\d{2}-\d{4}/', $dateString)) {
                return \Carbon\Carbon::createFromFormat('d-m-Y', $dateString);
            }
            // Format: DD/MM/YYYY
            if (preg_match('/\d{2}\/\d{2}\/\d{4}/', $dateString)) {
                return \Carbon\Carbon::createFromFormat('d/m/Y', $dateString);
            }
            // Format: YYYY-MM-DD
            if (preg_match('/\d{4}-\d{2}-\d{2}/', $dateString)) {
                return \Carbon\Carbon::parse($dateString);
            }
            // Format tanggal invalid (00-00-0000)
            if ($dateString === '00-00-0000' || $dateString === '0000-00-00') {
                return null;
            }
            
            return \Carbon\Carbon::parse($dateString);
        } catch (\Exception $e) {
            Log::warning("Gagal parse tanggal: {$dateString}");
            return null;
        }
    }
    
    /**
     * Generate email unik jika terjadi duplikat
     */
    private function makeUniqueEmail($email, $counter = 0)
    {
        $newEmail = $email;
        if ($counter > 0) {
            $parts = explode('@', $email);
            $newEmail = $parts[0] . $counter . '@' . $parts[1];
        }
        
        if (User::where('email', $newEmail)->exists()) {
            return $this->makeUniqueEmail($email, $counter + 1);
        }
        
        return $newEmail;
    }
    
    /**
     * Generate nomor anggota otomatis (jika tidak disediakan)
     */
    private function generateNoAnggota($jenis)
    {
        $kodeJenis = [
            'siswa' => 'SIS',
            'guru' => 'GRU',
            'pegawai' => 'PGW',
            'umum' => 'UMM',
            'admin' => 'ADM',
            'petugas' => 'PTG',
            'kepala_pustaka' => 'KPS',
            'pimpinan' => 'PMP'
        ][$jenis] ?? 'AGT';
        
        $tahun = date('y');
        $bulan = date('m');
        $prefix = $kodeJenis . $tahun . $bulan;
        
        $lastAnggota = User::where('no_anggota', 'like', $prefix . '%')
            ->orderBy('no_anggota', 'desc')
            ->first();
        
        $lastNumber = 0;
        if ($lastAnggota && $lastAnggota->no_anggota) {
            $lastNumber = intval(substr($lastAnggota->no_anggota, -4));
        }
        
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        return $prefix . $newNumber;
    }

    public function rules(): array
    {
        return [
            '*.nisn_nik' => 'nullable',
            '*.name' => 'nullable',
        ];
    }

    public function batchSize(): int
    {
        return 50;
    }

    public function chunkSize(): int
    {
        return 50;
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }

    public function getSkippedCount(): int
    {
        return $this->skipped;
    }
    
    public function getErrors(): array
    {
        return $this->errors;
    }
}