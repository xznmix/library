<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfilController extends Controller
{
    /**
     * Tampilkan profil anggota
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return view('anggota.pages.profil.index', compact('user'));
    }

    /**
     * Update profil anggota
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id)
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);
        
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? $user->phone;
        $user->address = $validated['address'] ?? $user->address;
        
        $saved = $user->save();
        
        if ($request->ajax()) {
            return response()->json([
                'success' => $saved,
                'message' => $saved ? 'Profil berhasil diperbarui' : 'Gagal memperbarui profil'
            ]);
        }
        
        if ($saved) {
            return redirect()->route('anggota.profil.index')
                ->with('success', 'Profil berhasil diperbarui');
        } else {
            return redirect()->route('anggota.profil.index')
                ->with('error', 'Gagal memperbarui profil');
        }
    }

    /**
     * Upload foto profil
     */
    public function uploadFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if ($user->foto_ktp && Storage::disk('public')->exists($user->foto_ktp)) {
            Storage::disk('public')->delete($user->foto_ktp);
        }
        
        $file = $request->file('foto');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('foto-profil', $filename, 'public');
        
        $user->foto_ktp = $path;
        $user->save();
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Foto profil berhasil diupload',
                'foto_url' => asset('storage/' . $path)
            ]);
        }
        
        return redirect()->route('anggota.profil.index')
            ->with('success', 'Foto profil berhasil diupload');
    }

    /**
     * Ubah password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);
        
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Cek password saat ini
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password saat ini salah'
            ], 422);
        }
        
        // Cek apakah password baru sama dengan yang lama
        if (Hash::check($request->new_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password baru tidak boleh sama dengan password lama'
            ], 422);
        }
        
        // Update password
        $user->password = Hash::make($request->new_password);
        $user->force_password_change = false;
        $user->save();
        
        // Logout user secara manual
        Auth::logout();
        
        // Hapus session
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah. Silakan login kembali.',
            'redirect' => route('login')
        ]);
    }
}