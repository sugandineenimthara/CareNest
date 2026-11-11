<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Child;
use App\Models\Immunization;
use App\Models\Midwife;
use App\Models\Mother;
use App\Models\Report;
use App\Models\TriposhaBook;
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

        
        if ($user->midwife && $user->midwife->area_id) {
            $areaId = $user->midwife->area_id;

            $placeholderMidwives = Midwife::where('area_id', $areaId)
                ->where('midwife_id', '!=', $user->midwife->midwife_id)
                ->where(function ($q) {
                    $q->whereNull('user_id')
                      ->orWhere('midwife_name', 'like', 'Midwife (%');
                })
                ->get();

            foreach ($placeholderMidwives as $placeholder) {
                Mother::where('midwife_id', $placeholder->midwife_id)->update(['midwife_id' => $user->midwife->midwife_id]);
                Child::where('midwife_id', $placeholder->midwife_id)->update(['midwife_id' => $user->midwife->midwife_id]);
                Attendance::where('midwife_id', $placeholder->midwife_id)->update(['midwife_id' => $user->midwife->midwife_id]);
                Immunization::where('midwife_id', $placeholder->midwife_id)->update(['midwife_id' => $user->midwife->midwife_id]);
                TriposhaBook::where('midwife_id', $placeholder->midwife_id)->update(['midwife_id' => $user->midwife->midwife_id]);
                Report::where('midwife_id', $placeholder->midwife_id)->update(['midwife_id' => $user->midwife->midwife_id]);

                $placeholder->delete();
            }
        }

        return redirect()->back()->with('success', 'Midwife approved successfully and assigned to area.');
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
            'area_id' => 'required|exists:areas,area_id'
        ]);

        
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        
        if ($user->midwife) {
            $user->midwife->midwife_name = $request->name;
            $user->midwife->area_id = $request->area_id;
            $user->midwife->save();

            
            $placeholderMidwives = Midwife::where('area_id', $request->area_id)
                ->where('midwife_id', '!=', $user->midwife->midwife_id)
                ->where(function ($q) {
                    $q->whereNull('user_id')
                      ->orWhere('midwife_name', 'like', 'Midwife (%');
                })
                ->get();

            foreach ($placeholderMidwives as $placeholder) {
                Mother::where('midwife_id', $placeholder->midwife_id)->update(['midwife_id' => $user->midwife->midwife_id]);
                Child::where('midwife_id', $placeholder->midwife_id)->update(['midwife_id' => $user->midwife->midwife_id]);
                Attendance::where('midwife_id', $placeholder->midwife_id)->update(['midwife_id' => $user->midwife->midwife_id]);
                Immunization::where('midwife_id', $placeholder->midwife_id)->update(['midwife_id' => $user->midwife->midwife_id]);
                TriposhaBook::where('midwife_id', $placeholder->midwife_id)->update(['midwife_id' => $user->midwife->midwife_id]);
                Report::where('midwife_id', $placeholder->midwife_id)->update(['midwife_id' => $user->midwife->midwife_id]);

                $placeholder->delete();
            }
        }

        return redirect()->route('admin.midwives.index')->with('success', 'Midwife details updated successfully.');
    }

    public function deleteMidwife($id)
    {
        $user = User::findOrFail($id);
        if ($user->role !== 'midwife') abort(403);

        
        if ($user->midwife) {
            $user->midwife->delete();
        }
        $user->delete();

        return redirect()->route('admin.midwives.index')->with('success', 'Midwife deleted successfully.');
    }
}
