<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'We could not find an account with that email address.',
        ]);

        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );

        $isFromChange = $request->input('from') === 'change-password' || \Illuminate\Support\Facades\Auth::check();
        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $request->email,
            'from_change_password' => $isFromChange ? 1 : null,
        ]);

        return back()->with('status', 'We have emailed your password reset link! (Reset Link: ' . $resetUrl . ')');
    }

    public function showResetForm(Request $request, $token)
    {
        $email = $request->email;
        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        // If token is valid and user is logged in or arrived from change password identity flow
        if ($record && Hash::check($token, $record->token)) {
            if (\Illuminate\Support\Facades\Auth::check() || $request->has('from_change_password')) {
                session([
                    'identity_authenticated' => true,
                    'authenticated_email' => $email,
                    'reset_token' => $token,
                ]);
                return redirect()->route('password.change')->with('success', 'Identity authenticated successfully via Forgot Password! Please set your new password below.');
            }
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'This password reset token is invalid or expired.']);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        if (\Illuminate\Support\Facades\Auth::check()) {
            return redirect()->route('password.change')->with('success', 'Your password has been reset successfully!');
        }

        return redirect()->route('login')->with('status', 'Your password has been reset successfully! You can now log in.');
    }
}
