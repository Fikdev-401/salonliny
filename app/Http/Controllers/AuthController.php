<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(array_merge($credentials, ['status' => 'aktif']))) {
            $request->session()->regenerate();
            
            $user = Auth::user();

            if ($user->must_change_password) {
                return redirect()->route('password.change');
            }
            
            // Redirect berdasarkan role
            if ($user->isSuperAdmin() || $user->isAdmin()) {
                return redirect()->intended('/dashboard');
            } elseif ($user->isPimpinan()) {
                return redirect()->intended('/dashboard');
            } elseif ($user->isKasir()) {
                return redirect()->intended('/transaksi');
            } elseif ($user->isKaryawanRole()) {
                return redirect()->intended('/absensi');
            }
            
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->must_change_password = false;
        $user->save();

        return back()->with('success', 'Password berhasil diubah.');
    }
}
