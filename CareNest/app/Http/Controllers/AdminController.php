<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function midwifeRequests()
    {
        $requests = User::where('role', 'midwife')->with('midwife.area')->get();
        return view('admin.midwife-requests', compact('requests'));
    }

    public function approveMidwife($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->role !== 'midwife') {
            abort(403);
        }

        $user->status = 'approved';
        $user->save();

        return redirect()->back()->with('success', 'Midwife approved successfully.');
    }

    public function rejectMidwife($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->role !== 'midwife') {
            abort(403);
        }

        $user->status = 'rejected';
        $user->save();

        return redirect()->back()->with('success', 'Midwife rejected.');
    }

    public function manageMidwives()
    {
        $pendingRequests = User::where('role', 'midwife')
                    ->where('status', 'pending')
                    ->with('midwife.area')
                    ->get();

        $midwives = User::where('role', 'midwife')
                    ->where('status', 'approved')
                    ->with('midwife.area')
                    ->get();
        return view('admin.midwives.index', compact('midwives', 'pendingRequests'));
    }

    public function editMidwife($id)
    {
        $user = User::findOrFail($id);
        if ($user->role !== 'midwife') abort(403);
        
        $areas = \App\Models\Area::all();
        return view('admin.midwives.edit', compact('user', 'areas'));
    }

    public function updateMidwife(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($user->role !== 'midwife') abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'area_id' => 'required|exists:area,area_id' // Assuming area table and area_id
        ]);

        // Update User
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        // Update Midwife details
        if ($user->midwife) {
            $user->midwife->area_id = $request->area_id;
            $user->midwife->save();
        }

        return redirect()->route('admin.midwives.index')->with('success', 'Midwife details updated successfully.');
    }

    public function deleteMidwife($id)
    {
        $user = User::findOrFail($id);
        if ($user->role !== 'midwife') abort(403);

        // Delete associated midwife record and user
        if ($user->midwife) {
            $user->midwife->delete();
        }
        $user->delete();

        return redirect()->route('admin.midwives.index')->with('success', 'Midwife deleted successfully.');
    }
}
