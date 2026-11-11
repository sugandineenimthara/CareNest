<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MotherRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role ?? 'admin';

        abort_unless(in_array($role, ['provider', 'admin', 'midwife'], true), 403);

        $query = User::where('role', 'mother')->with(['mother.midwife.area']);

        if ($role === 'midwife') {
            abort_unless($user->midwife, 403, 'Your midwife profile is not linked.');
            $midwifeId = $user->midwife->midwife_id;
            $query->whereHas('mother', function ($q) use ($midwifeId) {
                $q->where('midwife_id', $midwifeId);
            });
        }

        $requests = $query->orderByRaw("CASE WHEN status = 'pending' THEN 1 WHEN status = 'approved' THEN 2 ELSE 3 END")
            ->orderByDesc('created_at')
            ->get();

        $prefix = $role === 'midwife' ? 'midwife.' : 'admin.';

        return view('mother-requests.index', compact('requests', 'user', 'role', 'prefix'));
    }

    public function approve($id)
    {
        $authUser = Auth::user();
        $role = $authUser->role ?? 'admin';

        abort_unless(in_array($role, ['provider', 'admin', 'midwife'], true), 403);

        $user = User::with('mother')->findOrFail($id);

        if ($user->role !== 'mother') {
            abort(403);
        }

        if ($role === 'midwife') {
            abort_unless(
                $user->mother && $user->mother->midwife_id === $authUser->midwife->midwife_id,
                403,
                'You are not authorized to approve mothers registered outside your area.'
            );
        }

        $user->status = 'approved';
        $user->save();

        if ($user->mother && !$user->mother->user_id) {
            $user->mother->update(['user_id' => $user->id]);
        }

        return redirect()->back()->with('success', "Mother {$user->name} has been approved successfully and can now log into CareNest.");
    }

    public function reject($id)
    {
        $authUser = Auth::user();
        $role = $authUser->role ?? 'admin';

        abort_unless(in_array($role, ['provider', 'admin', 'midwife'], true), 403);

        $user = User::with('mother')->findOrFail($id);

        if ($user->role !== 'mother') {
            abort(403);
        }

        if ($role === 'midwife') {
            abort_unless(
                $user->mother && $user->mother->midwife_id === $authUser->midwife->midwife_id,
                403,
                'You are not authorized to reject mothers registered outside your area.'
            );
        }

        $user->status = 'rejected';
        $user->save();

        return redirect()->back()->with('success', "Registration request for Mother {$user->name} has been rejected.");
    }
}
