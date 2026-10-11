<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordController extends Controller
{
    /**
     * Show the Change Password view.
     */
    public function showChangePassword(Request $request)
    {
        $user = Auth::user();
        $role = $user->role ?? 'admin';
        $prefix = in_array($role, ['provider', 'admin']) ? 'admin.' : ($role === 'midwife' ? 'midwife.' : 'mother.');
        $identityVerified = session('identity_authenticated', false);

        return view('auth.change-password', compact('user', 'role', 'prefix', 'identityVerified'));
    }

    /**
     * Verify the current password via AJAX or stepped validation.
     */
    public function verifyCurrentPassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
        ]);

        $user = Auth::user();

        if (Hash::check($request->current_password, $user->password)) {
            session(['current_password_verified' => true]);
            return response()->json([
                'success' => true,
                'message' => 'Current password verified successfully.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'The current password entered is incorrect. You can authenticate your identity using the Forgot Password feature.',
        ], 422);
    }

    /**
     * Update the user password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        $isIdentityVerified = session('identity_authenticated', false);
        $isCurrentVerified = session('current_password_verified', false);

        // If identity wasn't authenticated via forgot password and current password wasn't verified
        if (!$isIdentityVerified && !$isCurrentVerified) {
            $request->validate([
                'current_password' => 'required|string',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if (!Hash::check($request->current_password, $user->password)) {
                return back()
                    ->withErrors(['current_password' => 'The current password entered is incorrect.'])
                    ->with('failed_current_password', true);
            }
        } else {
            $request->validate([
                'password' => 'required|string|min:6|confirmed',
            ]);
        }

        // Update user password
        $user->password = Hash::make($request->password);
        $user->save();

        // Clear verification states and token
        if ($user->email) {
            DB::table('password_reset_tokens')->where('email', $user->email)->delete();
        }
        session()->forget(['identity_authenticated', 'current_password_verified', 'authenticated_email', 'reset_token']);

        return redirect()->route('password.change')->with('success', 'Your password has been changed successfully!');
    }

    /**
     * Send identity authentication link for the logged in user using Forgot Password logic.
     */
    public function sendIdentityAuthLink(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->email) {
            return back()->with('error', 'No email found for this user account.');
        }

        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );

        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $user->email,
            'from_change_password' => 1,
        ]);

        return back()->with('status', 'Identity authentication link generated! You can authenticate your identity using this link: ' . $resetUrl);
    }
}
