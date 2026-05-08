<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\AnggotaHelper;

class KeanggotaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Query untuk PENDING (hanya yang benar-benar pending)
        $pending = User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])
                    ->where('status_anggota', 'pending')
                    ->orderBy('created_at', 'desc')
                    ->get();
        
        // Query untuk NON-PENDING (active, inactive, rejected)
        $query = User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])
                    ->where('status_anggota', '!=', 'pending');
        
        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('nisn_nik', 'LIKE', "%{$search}%")
                ->orWhere('no_anggota', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }
        
        // Filter status (untuk non-pending)
        if ($request->filled('status') && $request->status != 'pending') {
            $query->where('status_anggota', $request->status);
        }
        
        // Filter jenis
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        
        $anggota = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        // Statistik
        $statistik = [
            'total' => User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])->count(),
            'pending' => User::where('status_anggota', 'pending')->count(),
            'active' => User::where('status_anggota', 'active')->count(),
            'inactive' => User::where('status_anggota', 'inactive')->count(),
            'rejected' => User::where('status_anggota', 'rejected')->count(),
        ];
        
        return view('petugas.pages.keanggotaan.index', compact('anggota', 'pending', 'statistik'));
    }

    /**
     * Show the form for creating a new resource.
     * Untuk tambah anggota manual (langsung active tanpa verifikasi)
     */
    public function create()
    {
        return view('petugas.pages.keanggotaan.create');
    }

    /**
     * Store a newly created resource in storage.
     * Untuk tambah anggota manual
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nisn_nik' => 'required|string|unique:users,nisn_nik',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'jenis' => 'required|in:siswa,guru,pegawai,umum',
            'kelas' => 'nullable|string|max:20',
            'jurusan' => 'nullable|string|max:50',
        ]);
        
        $noAnggota = null; // <-- DEKLARASIKAN DI LUAR
        
        DB::transaction(function() use ($request, &$noAnggota) { // <-- PASS BY REFERENCE
            // 🔥 GENERATE NOMOR ANGGOTA DENGAN HELPER 🔥
            $noAnggota = AnggotaHelper::generateNoAnggota($request->jenis);
            
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->nisn_nik),
                'nisn_nik' => $request->nisn_nik,
                'phone' => $request->phone,
                'address' => $request->address,
                'jenis' => $request->jenis,
                'kelas' => $request->kelas,
                'jurusan' => $request->jurusan,
                'role' => $request->jenis,
                'status_anggota' => 'active',
                'no_anggota' => $noAnggota,
                'tanggal_daftar' => now(),
                'masa_berlaku' => now()->addYear(),
                'approved_at' => now(),
                'approved_by' => Auth::id(),
            ]);
        });
        
        return redirect()
            ->route('petugas.keanggotaan.index')
            ->with('success', "Anggota baru berhasil ditambahkan. No. Anggota: {$noAnggota}");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $calonAnggota = User::with(['peminjaman' => function($q) {
                              $q->with('buku')->latest();
                          }])->findOrFail($id);
        
        return view('petugas.pages.keanggotaan.show', compact('calonAnggota'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $anggota = User::findOrFail($id);
        return view('petugas.pages.keanggotaan.edit', compact('anggota'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $anggota = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $anggota->id,
            'nisn_nik' => 'required|string|unique:users,nisn_nik,' . $anggota->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'jenis' => 'required|in:siswa,guru,pegawai,umum',
            'kelas' => 'nullable|string|max:20',
            'jurusan' => 'nullable|string|max:50',
            'status_anggota' => 'required|in:pending,active,inactive,rejected',
            'masa_berlaku' => 'nullable|date',
        ]);
        
        $anggota->update($request->all());
        
        return redirect()
            ->route('petugas.keanggotaan.index')
            ->with('success', "Data anggota {$anggota->name} berhasil diperbarui.");
    }

    /**
     * Approve anggota (dari pending ke active)
     */
    public function approve(string $id)
    {
        $user = User::findOrFail($id);
        
        // 🔥 TAMBAHKAN: Cek apakah email sudah diverifikasi (opsional)
        // Jika ingin email harus verifikasi dulu sebelum approve, uncomment kode di bawah:
        /*
        if (!$user->email_verified_at) {
            return redirect()
                ->route('petugas.keanggotaan.index')
                ->with('error', "Anggota {$user->name} belum memverifikasi email. Silakan minta user verifikasi email terlebih dahulu.");
        }
        */
        
        DB::transaction(function() use ($user) {
            $noAnggota = AnggotaHelper::generateNoAnggota($user->jenis);
            
            $user->update([
                'status_anggota' => 'active',
                'no_anggota' => $noAnggota,
                'tanggal_daftar' => now(),
                'masa_berlaku' => now()->addYear(),
                'approved_at' => now(),
                'approved_by' => Auth::id(),
                'catatan_penolakan' => null,
                'processed_at' => now(),      // 🔥 TAMBAHKAN
                'processed_by' => Auth::id(), // 🔥 TAMBAHKAN
            ]);
        });
        
        return redirect()
            ->route('petugas.keanggotaan.index')
            ->with('success', "Anggota {$user->name} telah disetujui. No. Anggota: {$user->no_anggota}");
    }

    /**
     * Reject anggota
     */
    public function reject(Request $request, string $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|min:10'
        ]);
        
        $user = User::findOrFail($id);
        
        $user->update([
            'status_anggota' => 'rejected',
            'catatan_penolakan' => $request->alasan_penolakan,
            'approved_at' => now(),
            'approved_by' => Auth::id()
        ]);
        
        return redirect()
            ->route('petugas.keanggotaan.index')
            ->with('error', "Pendaftaran {$user->name} ditolak.");
    }

    /**
     * Deactivate anggota
     */
    public function deactivate(Request $request, string $id)
    {
        $request->validate([
            'alasan' => 'required|string'
        ]);
        
        $user = User::findOrFail($id);
        
        $user->update([
            'status_anggota' => 'inactive',
            'catatan_penolakan' => $request->alasan
        ]);
        
        return redirect()
            ->route('petugas.keanggotaan.index')
            ->with('warning', "Anggota {$user->name} telah dinonaktifkan.");
    }

    /**
     * Activate kembali anggota
     */
    public function activate(string $id)
    {
        $user = User::findOrFail($id);
        
        $user->update([
            'status_anggota' => 'active',
            'catatan_penolakan' => null
        ]);
        
        return redirect()
            ->route('petugas.keanggotaan.index')
            ->with('success', "Anggota {$user->name} telah diaktifkan kembali.");
    }

    /**
     * Export data anggota
     */
    public function export()
    {
        // Bisa diisi dengan Laravel Excel nanti
        return redirect()
            ->route('petugas.keanggotaan.index')
            ->with('info', 'Fitur export sedang dalam pengembangan');
    }
}