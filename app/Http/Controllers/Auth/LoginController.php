<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $login    = $request->input('login');     // bisa email atau NIM
        $password = $request->input('password');

        // Deteksi field yang dipakai
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'txtEmail' : 'txtNim';

        // Penting: kunci password DIKIRIM sebagai 'password' (bukan txtPassword).
        // Guard akan memanggil getAuthPassword() di model untuk membandingkan hash.
        $credentials = [
            $field      => $login,
            'password'  => $password,
            'bitActive' => 1, // hanya akun aktif
        ];

        if (Auth::attempt($credentials, false)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withErrors(['login' => 'Kredensial salah atau akun tidak aktif.'])
            ->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
