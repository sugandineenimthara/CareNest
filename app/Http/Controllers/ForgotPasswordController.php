<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
            'email.exists' => 'No account found with that email address.',
        ]);

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_otp_codes')->where('email', $request->email)->delete();
        DB::table('password_otp_codes')->insert([
            'email'      => $request->email,
            'code'       => $code,
            'expires_at' => Carbon::now()->addMinutes(10),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        try {
            Mail::send([], [], function ($message) use ($request, $code) {
                $message->to($request->email)
                    ->subject('CareNest – Your Password Reset Code')
                    ->html(
                        '<div style="font-family:sans-serif;max-width:480px;margin:auto;padding:32px;background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0;">'
                        . '<h2 style="color:#1b4d3e;margin-bottom:8px;">Password Reset Code</h2>'
                        . '<p style="color:#475569;font-size:14px;margin-bottom:24px;">Use the code below to reset your CareNest password. It expires in <strong>10 minutes</strong>.</p>'
                        . '<div style="font-size:36px;font-weight:800;letter-spacing:12px;text-align:center;padding:20px;background:#fff;border-radius:10px;border:2px dashed #00c853;color:#1b4d3e;">'
                        . $code
                        . '</div>'
                        . '<p style="color:#94a3b8;font-size:12px;margin-top:24px;">If you did not request this, ignore this email.</p>'
                        . '</div>'
                    );
            });
        } catch (\Exception $e) {
            Log::error('Brevo OTP send failed: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Failed to send OTP email. Please check mail configuration.']);
        }

        session(['otp_email' => $request->email]);

        return redirect()->route('password.otp.form')
            ->with('status', 'A 6-digit code has been sent to ' . $request->email);
    }

    public function showOtpForm(Request $request)
    {
        $email = session('otp_email', $request->query('email', ''));
        return view('auth.verify-otp', compact('email'));
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|digits:6',
        ]);

        $record = DB::table('password_otp_codes')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return back()->withErrors(['code' => 'No OTP found for this email. Please request a new one.'])
                ->withInput();
        }

        if (Carbon::parse($record->expires_at)->isPast()) {
            DB::table('password_otp_codes')->where('email', $request->email)->delete();
            return back()->withErrors(['code' => 'This code has expired. Please request a new one.'])
                ->withInput();
        }

        if ($record->code !== $request->code) {
            return back()->withErrors(['code' => 'Invalid code. Please try again.'])
                ->withInput();
        }

        DB::table('password_otp_codes')->where('email', $request->email)->delete();

        session([
            'otp_verified_email' => $request->email,
            'otp_verified_at'    => now()->timestamp,
        ]);

        return redirect()->route('password.otp.reset');
    }

    public function showOtpResetForm()
    {
        $email = session('otp_verified_email');

        if (!$email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Session expired. Please start again.']);
        }

        return view('auth.otp-reset-password', compact('email'));
    }

    public function resetViaOtp(Request $request)
    {
        $email = session('otp_verified_email');

        if (!$email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Session expired. Please start again.']);
        }

        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Account not found.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        session()->forget(['otp_verified_email', 'otp_verified_at', 'otp_email']);

        return redirect()->route('login')
            ->with('status', 'Password reset successfully! You can now log in.');
    }

    public function showResetForm(Request $request, $token)
    {
        $email = $request->email;
        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if ($record && Hash::check($token, $record->token)) {
            if (\Illuminate\Support\Facades\Auth::check() || $request->has('from_change_password')) {
                session([
                    'identity_authenticated' => true,
                    'authenticated_email'    => $email,
                    'reset_token'            => $token,
                ]);
                return redirect()->route('password.change')
                    ->with('success', 'Identity verified. Please set your new password.');
            }
        }

        return view('auth.reset-password', compact('token', 'email'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

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

        return redirect()->route('login')->with('status', 'Your password has been reset! You can now log in.');
    }
}
