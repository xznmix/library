<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ForcePasswordController extends Controller
{
    public function index()
    {
        return view('auth.force-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->update([
            'password' => Hash::make($request->password),
            'force_password_change' => false,
        ]);

        return redirect()->route('dashboard');
    }
}
