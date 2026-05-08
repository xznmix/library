<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Carbon\Carbon;
use App\Models\User; // Tambahkan ini

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        
        // Update last login timestamp dengan type hinting yang benar
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if ($user) {
            $user->last_login_at = Carbon::now();
            $user->save(); // Sekarang seharusnya tidak error
        }

        // Redirect berdasarkan role
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        } elseif ($user->role === 'petugas') {
            return redirect()->intended(route('petugas.dashboard', absolute: false));
        } elseif (in_array($user->role, ['siswa', 'guru', 'pegawai', 'umum'])) {
            return redirect()->intended(route('anggota.dashboard', absolute: false));
        } elseif ($user->role === 'kepala_pustaka') {
            return redirect()->intended(route('kepala-pustaka.dashboard', absolute: false));
        } elseif ($user->role === 'pimpinan') {
            return redirect()->intended(route('pimpinan.dashboard', absolute: false));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}