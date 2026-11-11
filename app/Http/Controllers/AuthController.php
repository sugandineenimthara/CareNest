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

        $loginType = filter_var($credentials['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        
        $attemptData = [
            $loginType => $credentials['email'],
            'password' => $credentials['password'],
        ];

        if (Auth::attempt($attemptData, $request->boolean('remember'))) {
            $user = Auth::user();

            if ($user->status === 'pending') {
                Auth::logout();
                $msg = $user->role === 'mother'
                    ? 'Your registration is currently awaiting approval by your area midwife.'
                    : 'Your registration is currently awaiting administrator approval.';
                return back()->with('error', $msg);
            }

            if ($user->status === 'rejected') {
                Auth::logout();
                $msg = $user->role === 'mother'
                    ? 'Your registration request was not approved. Please contact your area midwife.'
                    : 'Your account has been rejected.';
                return back()->with('error', $msg);
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
                return redirect()->route('mother.profile');
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
            'mother_id'     => 'nullable|exists:mothers,mother_id',
            'mother_name'   => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users,email',
            'password'      => 'required|string|min:6|confirmed',
            'phone_no'      => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'address'       => 'nullable|string|max:500',
            'midwife_id'    => 'nullable|exists:midwives,midwife_id',
        ]);

        if ($request->filled('mother_id')) {
            $mother = Mother::with(['midwife.area', 'user'])->findOrFail($validated['mother_id']);

            if ($mother->user_id && $mother->user) {
                if ($mother->user->status === 'approved') {
                    return back()->withInput()->withErrors([
                        'mother_id' => 'An active user account is already approved for Mother ID #' . $mother->mother_id . '. Please log in.',
                    ]);
                }
                if ($mother->user->status === 'pending') {
                    return back()->withInput()->withErrors([
                        'mother_id' => 'A registration request for Mother ID #' . $mother->mother_id . ' is already awaiting midwife approval.',
                    ]);
                }
            }

            $user = User::create([
                'name'     => $validated['mother_name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role'     => 'mother',
                'status'   => 'pending',
            ]);

            $mother->user_id = $user->id;
            if (!empty($validated['phone_no']) && empty($mother->phone_no)) {
                $mother->phone_no = $validated['phone_no'];
            }
            if (!empty($validated['date_of_birth']) && empty($mother->date_of_birth)) {
                $mother->date_of_birth = $validated['date_of_birth'];
            }
            if (!empty($validated['address']) && empty($mother->address)) {
                $mother->address = $validated['address'];
            }
            $mother->save();

            $midwifeName = $mother->midwife ? $mother->midwife->midwife_name : 'your area midwife';

            return redirect()->route('login')->with('success', "Registration request submitted successfully! Your account is awaiting approval by {$midwifeName}. You can log in once approved.");
        }

        $midwifeId = $validated['midwife_id'] ?? Midwife::query()->value('midwife_id');

        $user = User::create([
            'name'     => $validated['mother_name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'mother',
            'status'   => 'pending',
        ]);

        Mother::create([
            'mother_name'   => $validated['mother_name'],
            'phone_no'      => $validated['phone_no'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'address'       => $validated['address'] ?? null,
            'midwife_id'    => $midwifeId,
            'user_id'       => $user->id,
        ]);

        return redirect()->route('login')->with('success', 'Registration request submitted successfully! Your account is awaiting approval by your area midwife. You can log in once approved.');
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
