<?php

namespace App\Http\Controllers\Koki;

use App\Http\Controllers\Controller;
use App\Models\Koki;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class KokiAuthController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register-koki');
    }

    // Registrasi Koki
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:kokis,email',
            'no_hp' => 'required|string|max:15',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $koki = Koki::create([
            'name' => $request->name,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'password' => Hash::make($request->password),
            'status' => 'pending',
        ]);

        Auth::guard('kokis')->login($koki);  // Autentikasi koki setelah registrasi

        return redirect()->route('koki.login')->with('success', 'Registrasi berhasil! menunggu  beberapa menit untuk aproved data.');
    }

    public function showLoginForm()
    {
        return view('auth.koki-login');
    }

    // Menangani proses login koki
    public function login(Request $request)
    {
        // Validate login data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);



        if (Auth::guard('kokis')->attempt($request->only('email', 'password'))) {
            $koki = Auth::guard('kokis')->user();

            Log::info($koki->status);

            // Cek status koki
            if ($koki->status === 'pending' || $koki->status === 'rejected') {
                // Logout dan arahkan kembali dengan pesan bahwa akun masih pending
                Auth::guard('kokis')->logout();
                return redirect()->route('koki.login')->with('error', 'Akun Anda masih menunggu persetujuan admin.');
            }

            // Jika status koki sudah disetujui, arahkan ke dashboard koki
            return redirect()->route('koki.dashboard');
        }

        // Jika login gagal, kembalikan dengan error
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }
}
