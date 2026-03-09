<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Log;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('redirect.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return back()->with('error', 'Username tidak ditemukan!');
        }

        if ($user->status == 'nonaktif') {
            return back()->with('error', 'Akun Anda telah dinonaktifkan. Hubungi administrator!');
        }

        if (Hash::check($request->password, $user->password)) {
            Auth::login($user);
            
            // Log aktivitas
            Log::create([
                'id_user' => $user->id,
                'activity' => "Login ke sistem sebagai {$user->role}"
            ]);

            return redirect()->route('redirect.dashboard');
        }

        return back()->with('error', 'Password salah!');
    }

    public function redirectDashboard()
    {
        if (Auth::check()) {
            $role = Auth::user()->role;
            
            switch ($role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'kasir':
                    return redirect()->route('kasir.transactions.dashboard');
                case 'owner':
                    return redirect()->route('owner.dashboard');
                default:
                    Auth::logout();
                    return redirect()->route('login');
            }
        }
        
        return redirect()->route('login');
    }

    public function logout()
    {
        if (Auth::check()) {
            Log::create([
                'id_user' => Auth::id(),
                'activity' => "Logout dari sistem"
            ]);
        }
        
        Auth::logout();
        return redirect()->route('login');
    }
}