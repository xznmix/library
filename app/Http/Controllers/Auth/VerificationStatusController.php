<?php
// app/Http/Controllers/Auth/VerificationStatusController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class VerificationStatusController extends Controller
{
    /**
     * Menampilkan form cek status verifikasi
     */
    public function showCheckForm()
    {
        return view('auth.check-verification');
    }
    
    /**
     * Memeriksa status verifikasi berdasarkan email
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.exists' => 'Email tidak ditemukan. Silakan periksa kembali atau daftar terlebih dahulu.'
        ]);
        
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->with('error', 'Email tidak ditemukan. Silakan periksa kembali atau daftar terlebih dahulu.');
        }
        
        // Simpan email ke session untuk kemudahan
        Session::put('check_email', $user->email);
        
        return redirect()->route('verification.status', ['email' => $user->email]);
    }
    
    /**
     * Menampilkan detail status verifikasi
     */
    public function showStatus($email)
    {
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return redirect()->route('verification.check.form')
                ->with('error', 'Data tidak ditemukan');
        }
        
        $status = $user->verification_status ?? 'pending';
        $message = '';
        
        switch ($status) {
            case 'approved':
                $message = 'Selamat! Pendaftaran Anda telah disetujui oleh petugas. Silakan login menggunakan NIK sebagai password default.';
                break;
            case 'rejected':
                $message = 'Mohon maaf, pendaftaran Anda ditolak. Silakan periksa alasan penolakan di bawah ini.';
                break;
            default:
                $message = 'Pendaftaran Anda sedang dalam proses verifikasi oleh petugas. Proses ini memakan waktu maksimal 1x24 jam.';
        }
        
        return view('auth.verification-status', [
            'status' => $status,
            'message' => $message,
            'name' => $user->name,
            'email' => $user->email,
            'member_number' => $user->member_number,
            'rejection_reason' => $user->rejection_reason,
            'verified_at' => $user->verified_at,
            'created_at' => $user->created_at,
        ]);
    }
    
    /**
     * Cek status via AJAX untuk auto refresh
     */
    public function checkStatusAjax(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json(['status' => 'not_found']);
        }
        
        return response()->json([
            'status' => $user->verification_status ?? 'pending',
            'member_number' => $user->member_number,
            'rejection_reason' => $user->rejection_reason,
        ]);
    }
}