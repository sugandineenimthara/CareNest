<?php

namespace App\Http\Controllers;

use App\Models\Midwife;
use App\Models\Mother;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Support login by email or name
        $loginType = filter_var($credentials['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        
        $attemptData = [
            $loginType => $credentials['email'],
            'password' => $credentials['password'],
        ];

        if (Auth::attempt($attemptData, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:mother,provider',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        // Automatically link to default midwife or mother record if available
        if ($validated['role'] === 'mother') {
            $midwife = Midwife::first();
            if ($midwife) {
                Mother::firstOrCreate(
                    ['phone_no' => $validated['email']],
                    [
                        'mother_name' => $validated['name'],
                        'midwife_id' => $midwife->midwife_id,
                        'address' => 'Registered via CareNest App',
                    ]
                );
            }
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function dashboard()
    {
        $user = Auth::user();
        return view('dashboard', compact('user'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
