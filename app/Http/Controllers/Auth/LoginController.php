<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin(Request $request)
    {
        if (Auth::check() && !$request->has('add_account')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $previousId = Auth::id(); // Might be null if guest

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $newId = Auth::id();

            if (!Auth::user()->is_active) {
                Auth::logout();
                // Jika sebelumnya login sebagai user lain, kembalikan sesinya
                if ($previousId && $previousId != $newId) {
                    Auth::loginUsingId($previousId);
                }
                return back()->withErrors(['email' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.']);
            }

            // Simpan akun ke session untuk multi-account
            $accounts = session('multi_accounts', []);
            // Masukkan akun sebelumnya jika ada
            if ($previousId && $previousId != $newId && !in_array($previousId, $accounts)) {
                $accounts[] = $previousId;
            }
            
            // Masukkan akun saat ini jika belum ada
            if (!in_array($newId, $accounts)) {
                $accounts[] = $newId;
            } else if ($previousId && $previousId == $newId) {
                // Login ke akun yang sudah aktif, tidak perlu merubah apa-apa
            } else {
                // Login ke akun yang sudah ada di list tapi tidak aktif, tetap masukkan
            }
            session(['multi_accounts' => $accounts]);

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function switchAccount(Request $request, $id)
    {
        $accounts = session('multi_accounts', []);
        
        if (in_array($id, $accounts)) {
            Auth::loginUsingId($id);
            return redirect()->route('dashboard');
        }
        
        return redirect()->route('dashboard')->with('error', 'Gagal mengganti akun.');
    }

    public function logout(Request $request)
    {
        $currentId = Auth::id();
        $accounts = session('multi_accounts', []);
        
        // Hapus akun yang sedang aktif dari daftar
        $accounts = array_filter($accounts, function($id) use ($currentId) {
            return $id != $currentId;
        });
        
        // Re-index array
        $accounts = array_values($accounts);

        Auth::logout();

        if (count($accounts) > 0) {
            // Jika masih ada akun lain, switch ke akun pertama di daftar
            $nextAccount = $accounts[0];
            Auth::loginUsingId($nextAccount);
            session(['multi_accounts' => $accounts]);
            return redirect()->route('dashboard')->with('success', 'Berhasil keluar. Dialihkan ke akun yang tersimpan.');
        } else {
            // Jika tidak ada akun lain
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/login')->with('success', 'Berhasil keluar dari sistem.');
        }
    }
}
