<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\Events\Registered;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|string|size:16|unique:users,nisn_nik',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:15',
            'pekerjaan' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'jenis' => 'required|in:umum',
            'foto_ktp' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $file = $request->file('foto_ktp');
        $tempPath = $file->store('temp_register', 'public');
        
        $data = [
            'name' => $request->name,
            'nik' => $request->nik,
            'email' => $request->email,
            'phone' => $request->phone,
            'pekerjaan' => $request->pekerjaan,
            'address' => $request->address,
            'jenis' => $request->jenis,
            'foto_ktp_temp_path' => $tempPath,
            'foto_ktp_original_name' => $file->getClientOriginalName(),
        ];

        session(['register_data' => $data]);
        
        return redirect()->route('register.confirm');
    }
    
    public function confirm()
    {
        if (!session()->has('register_data')) {
            return redirect()->route('register');
        }
        
        $data = session('register_data');
        return view('auth.register-confirm', ['data' => $data]);
    }
    
    public function submit(Request $request)
    {
        $data = session('register_data');
        
        if (!$data) {
            return redirect()->route('register')
                ->with('error', 'Sesi pendaftaran telah berakhir.');
        }

        if (!isset($data['foto_ktp_temp_path']) || !Storage::disk('public')->exists($data['foto_ktp_temp_path'])) {
            session()->forget('register_data');
            return redirect()->route('register')
                ->with('error', 'File KTP tidak ditemukan. Silakan daftar ulang.');
        }

        $oldPath = $data['foto_ktp_temp_path'];
        $extension = pathinfo($oldPath, PATHINFO_EXTENSION);
        $newFileName = 'ktp_' . date('Ymd_His') . '_' . Str::random(10) . '.' . $extension;
        $newPath = 'pendaftar/ktp/' . $newFileName;
        
        Storage::disk('public')->move($oldPath, $newPath);

        // 🔥 TANPA VERIFIKASI EMAIL - Langsung aktif
        $userData = [
            'name' => $data['name'],
            'nisn_nik' => $data['nik'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'pekerjaan' => $data['pekerjaan'] ?? null,
            'address' => $data['address'] ?? null,
            'jenis' => 'umum',
            'role' => 'umum', 
            'password' => Hash::make($data['nik']),
            'status_anggota' => 'pending',
            'status' => 'active',
            'foto_ktp' => $newPath,
            'submitted_at' => now(),
            'email_verified_at' => now(), // Langsung verified
        ];
        
        $user = User::create($userData);
        
        event(new Registered($user));
        
        session()->forget('register_data');
        session(['pending_email' => $user->email]);
        
        return redirect()->route('register.pending');
    }
    
    public function pending()
    {
        if (!session()->has('pending_email')) {
            return redirect()->route('register');
        }
        
        return view('auth.register-pending');
    }
}