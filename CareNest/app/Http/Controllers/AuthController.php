<?php

namespace App\Http\Controllers;

use App\Models\Area;
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
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Support both email and username if applicable
        $loginType = filter_var($credentials['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        
        $attemptData = [
            $loginType => $credentials['email'],
            'password' => $credentials['password'],
        ];

        if (Auth::attempt($attemptData, $request->boolean('remember'))) {
            $user = Auth::user();

            if ($user->status === 'pending') {
                Auth::logout();
                return back()->with('error', 'Your registration is currently awaiting administrator approval.');
            }

            if ($user->status === 'rejected') {
                Auth::logout();
                return back()->with('error', 'Your account has been rejected.');
            }

            $request->session()->regenerate();
            return $this->redirectBasedOnRole($user);
        }

        return back()->withErrors([
            'email' => 'Invalid login credentials.',
        ])->onlyInput('email');
    }

    private function redirectBasedOnRole($user)
    {
        switch ($user->role) {
            case 'provider':
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'midwife':
                return redirect()->route('midwife.dashboard');
            case 'mother':
                return redirect()->route('mother.dashboard');
            default:
                Auth::logout();
                return redirect()->route('login')->with('error', 'Invalid role assigned to your account.');
        }
    }

    public function showRegisterMother()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        $midwives = Midwife::all();
        return view('auth.register_mother', compact('midwives'));
    }

    public function registerMother(Request $request)
    {
        $validated = $request->validate([
            'mother_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone_no' => 'required|string|max:15',
            'date_of_birth' => 'required|date',
            'address' => 'required|string|max:500',
            'midwife_id' => 'required|exists:midwives,midwife_id',
        ]);

        $user = User::create([
            'name' => $validated['mother_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'mother',
            'status' => 'approved',
        ]);

        Mother::create([
            'mother_name' => $validated['mother_name'],
            'phone_no' => $validated['phone_no'],
            'date_of_birth' => $validated['date_of_birth'],
            'address' => $validated['address'],
            'midwife_id' => $validated['midwife_id'],
            'user_id' => $user->id,
        ]);

        return redirect()->route('login')->with('success', 'Registration completed successfully. You can now login.');
    }

    public function showRegisterMidwife()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        $areas = Area::all();
        return view('auth.register_midwife', compact('areas'));
    }

    public function registerMidwife(Request $request)
    {
        $validated = $request->validate([
            'midwife_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'area_id' => 'required|exists:areas,area_id',
        ]);

        $user = User::create([
            'name' => $validated['midwife_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'midwife',
            'status' => 'pending',
        ]);

        Midwife::create([
            'midwife_name' => $validated['midwife_name'],
            'area_id' => $validated['area_id'],
            'user_id' => $user->id,
        ]);

        return redirect()->route('login')->with('success', 'Your registration request has been submitted successfully and is waiting for administrator approval.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
