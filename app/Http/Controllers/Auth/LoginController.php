<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Models\MahasiswaProfile;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $login    = $request->input('login');
        $password = $request->input('password');

        // Cek apakah input adalah email
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);

        if ($isEmail) {
            $credentials = [
                'txtEmail'  => $login,
                'password'  => $password,
                'bitActive' => 1,
            ];

            if (Auth::attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();
                return redirect()->intended(route('dashboard'))->with('success', 'Berhasil masuk.');
            }

        } else {
            $profile = MahasiswaProfile::where('txtNIM', $login)->first();

            if ($profile) {
                $user = $profile->user;

                if ($user && $user->bitActive && Hash::check($password, $user->txtPassword)) {

                    Auth::login($user, $request->boolean('remember'));
                    $request->session()->regenerate();
                    return redirect()->intended(route('dashboard'))->with('success', 'Berhasil masuk.');
                }
            }
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
        return redirect()->intended(route('login'))->with('success', 'Berhasil keluar.');
    }
}
