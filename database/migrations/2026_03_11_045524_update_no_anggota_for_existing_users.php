<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Helpers\AnggotaHelper;

return new class extends Migration
{
    public function up(): void
    {
        // Ambil semua anggota yang belum punya no_anggota
        $users = User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])
            ->whereNull('no_anggota')
            ->get();
        
        foreach ($users as $user) {
            User::where('id', $user->id)->update([
                'no_anggota' => AnggotaHelper::generateNoAnggota($user->jenis ?? $user->role)
            ]);
        }
    }

    public function down(): void
    {
        // Tidak perlu di-rollback
    }
};