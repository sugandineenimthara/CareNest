<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Child;
use App\Models\Immunization;
use App\Models\Midwife;
use App\Models\Mother;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChildController extends Controller
{
    /**
     * Display all children records separated by clinic areas.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Child::class);

        $user = Auth::user();
        $search = $request->query('search');
        $areaFilter = $request->query('area_id');

        $allAreas = Area::all();

        // Get areas and their children, with mothers and immunizations
        $areasQuery = Area::query();
        if ($areaFilter) {
            $areasQuery->where('area_id', $areaFilter);
        }

        $areas = $areasQuery->get()->map(function ($area) use ($search, $user) {
            $childrenQuery = Child::with(['mother.midwife', 'midwife', 'immunizations'])
                ->where(function ($q) use ($area) {
                    $q->where('area_id', $area->area_id)
                      ->orWhereHas('midwife', function ($mq) use ($area) {
                          $mq->where('area_id', $area->area_id);
                      });
                });

            if ($search) {
                $childrenQuery->where(function ($q) use ($search) {
                    $q->where('child_id', 'like', "%{$search}%")
                      ->orWhere('child_name', 'like', "%{$search}%")
                      ->orWhere('gender', 'like', "%{$search}%")
                      ->orWhereHas('mother', function ($mq) use ($search) {
                          $mq->where('mother_id', 'like', "%{$search}%")
                            ->orWhere('mother_name', 'like', "%{$search}%")
                            ->orWhere('phone_no', 'like', "%{$search}%");
                      });
                });
            }

            if ($user->role === 'mother') {
                if (!$user->mother) abort(403, 'Your mother profile is not linked or incomplete. Please contact the administrator.');
                $childrenQuery->where('mother_id', $user->mother->mother_id);
            } elseif ($user->role === 'midwife' && $user->midwife) {
                // Midwife can only see children in their assigned area
                $childrenQuery->where(function($q) use ($user) {
                    $q->where('midwife_id', $user->midwife->midwife_id)
                      ->orWhereHas('mother', function ($mq) use ($user) {
                          $mq->where('midwife_id', $user->midwife->midwife_id);
                      });
                });
            }

            $area->filtered_children = $childrenQuery->orderBy('date_of_birth', 'desc')->get();
            return $area;
        });

        // Summary Statistics
        $totalChildren = Child::count();
        $totalAreas = Area::count();
        $maleChildren = Child::where('gender', 'Male')->count();
        $femaleChildren = Child::where('gender', 'Female')->count();
        $bcgVaccinatedCount = Child::where('bcg_vaccinated_at_birth', true)->count();

        return view('children.index', compact(
            'user',
            'areas',
            'search',
            'areaFilter',
            'allAreas',
            'totalChildren',
            'totalAreas',
            'maleChildren',
            'femaleChildren',
            'bcgVaccinatedCount'
        ));
    }

    /**
     * Show registration form for a new child.
     */
    public function create(Request $request)
    {
        $this->authorize('create', Child::class);

        $user = Auth::user();
        $selectedMotherId = $request->query('mother_id');
        $mothers = Mother::with(['midwife.area'])->orderBy('mother_name')->get();
        $midwives = Midwife::with('area')->get();
        $areas = Area::all();

        return view('children.create', compact('user', 'mothers', 'midwives', 'areas', 'selectedMotherId'));
    }

    /**
     * Store a new child and optionally record BCG vaccination.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Child::class);

        $validated = $request->validate([
            'mother_id' => 'required|exists:mothers,mother_id',
            'child_name' => 'nullable|string|max:255',
            'gender' => 'required|in:Male,Female',
            'date_of_birth' => 'required|date|before_or_equal:today',
            'birth_weight' => 'required|numeric|min:0.5|max:10',
            'birth_length' => 'nullable|numeric|min:20|max:80',
            'midwife_id' => 'nullable|exists:midwives,midwife_id',
            'area_id' => 'nullable|exists:areas,area_id',
            'bcg_vaccinated_at_birth' => 'nullable|boolean',
            'bcg_batch_no' => 'nullable|string|max:100',
            'bcg_vaccinated_date' => 'nullable|date',
            'health_details' => 'nullable|string|max:1000',
        ]);

        $mother = Mother::with('midwife')->findOrFail($validated['mother_id']);
        $midwifeId = $validated['midwife_id'] ?? ($mother->midwife_id ?? Midwife::first()->midwife_id);
        $areaId = $validated['area_id'] ?? ($mother->midwife?->area_id ?? Midwife::find($midwifeId)?->area_id);

        $bcgGiven = !empty($request->bcg_vaccinated_at_birth);

        DB::beginTransaction();
        try {
            $child = Child::create([
                'mother_id' => $validated['mother_id'],
                'midwife_id' => $midwifeId,
                'area_id' => $areaId,
                'child_name' => $validated['child_name'] ?? null,
                'gender' => $validated['gender'],
                'date_of_birth' => $validated['date_of_birth'],
                'birth_weight' => $validated['birth_weight'],
                'birth_length' => $validated['birth_length'] ?? null,
                'bcg_vaccinated_at_birth' => $bcgGiven,
                'bcg_batch_no' => $bcgGiven ? ($validated['bcg_batch_no'] ?? 'BCG-' . date('Ymd')) : null,
                'bcg_vaccinated_date' => $bcgGiven ? ($validated['bcg_vaccinated_date'] ?? $validated['date_of_birth']) : null,
                'health_details' => $validated['health_details'] ?? null,
            ]);

            // If BCG was vaccinated before 24 hrs from birth, also record into immunizations table
            if ($bcgGiven) {
                Immunization::create([
                    'child_id' => $child->child_id,
                    'midwife_id' => $midwifeId,
                    'batch_no' => $validated['bcg_batch_no'] ?? ('BCG-' . date('Ymd')),
                    'vaccine_name' => 'BCG',
                    'dose' => 'Single Dose',
                    'age' => 'Birth (within 24 hrs)',
                    'immunization_date' => $validated['bcg_vaccinated_date'] ?? $validated['date_of_birth'],
                    'status' => 'Completed',
                    'remarks' => 'Vaccinated within 24 hours from birth'
                ]);
            }

            DB::commit();

            return redirect()->route($user->role === 'midwife' ? 'midwife.children.show' : 'admin.children.show', $child->child_id)
                ->with('success', 'Child registered successfully and linked to Mother ' . $mother->mother_name . ' (ID: ' . $mother->mother_id . ')');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error registering child: ' . $e->getMessage());
        }
    }

    /**
     * Display a specific child record with linked mother details and immunization schedule.
     */
    public function show(int $id)
    {
        $child = Child::with(['mother.midwife.area', 'midwife.area', 'area', 'immunizations.midwife'])->findOrFail($id);
        $this->authorize('view', $child);

        $user = Auth::user();

        $standardSchedule = Child::getVaccinationSchedule();
        $childDob = Carbon::parse($child->date_of_birth);
        $childAgeMonths = $childDob->diffInMonths(now());

        $scheduleStatus = [];
        foreach ($standardSchedule as $vaccine) {
            // Find if immunization matches this vaccine name
            $recorded = $child->immunizations->first(function ($imm) use ($vaccine) {
                $targetPrefix = explode(' ', $vaccine['vaccine_name'])[0];
                return stripos($imm->vaccine_name, $targetPrefix) !== false;
            });

            $dueDate = $childDob->copy()->addMonths($vaccine['months_due']);

            if ($recorded) {
                $status = 'Completed';
            } elseif (now()->greaterThanOrEqualTo($dueDate->copy()->addMonth())) {
                $status = 'Overdue';
            } elseif (now()->greaterThanOrEqualTo($dueDate)) {
                $status = 'Due Now';
            } else {
                $status = 'Upcoming';
            }

            $scheduleStatus[] = [
                'id' => $vaccine['id'],
                'vaccine_name' => $vaccine['vaccine_name'],
                'target_period' => $vaccine['target_period'],
                'dose' => $vaccine['dose'],
                'description' => $vaccine['description'],
                'due_date' => $dueDate->format('Y-m-d'),
                'status' => $status,
                'record' => $recorded,
            ];
        }

        $midwives = Midwife::with('area')->get();

        return view('children.show', compact('user', 'child', 'scheduleStatus', 'childAgeMonths', 'midwives'));
    }

    /**
     * Show form for editing child record.
     */
    public function edit(int $id)
    {
        $child = Child::with(['mother', 'midwife'])->findOrFail($id);
        $this->authorize('update', $child);

        $user = Auth::user();
        $mothers = Mother::with(['midwife.area'])->orderBy('mother_name')->get();
        $midwives = Midwife::with('area')->get();
        $areas = Area::all();

        return view('children.edit', compact('user', 'child', 'mothers', 'midwives', 'areas'));
    }

    /**
     * Update child record.
     */
    public function update(Request $request, int $id)
    {
        $child = Child::findOrFail($id);
        $this->authorize('update', $child);

        $validated = $request->validate([
            'mother_id' => 'required|exists:mothers,mother_id',
            'child_name' => 'nullable|string|max:255',
            'gender' => 'required|in:Male,Female',
            'date_of_birth' => 'required|date|before_or_equal:today',
            'birth_weight' => 'required|numeric|min:0.5|max:10',
            'birth_length' => 'nullable|numeric|min:20|max:80',
            'midwife_id' => 'nullable|exists:midwives,midwife_id',
            'area_id' => 'nullable|exists:areas,area_id',
            'bcg_vaccinated_at_birth' => 'nullable|boolean',
            'bcg_batch_no' => 'nullable|string|max:100',
            'bcg_vaccinated_date' => 'nullable|date',
            'health_details' => 'nullable|string|max:1000',
        ]);

        $bcgGiven = !empty($request->bcg_vaccinated_at_birth);
        $validated['bcg_vaccinated_at_birth'] = $bcgGiven;

        $child->update($validated);

        return redirect()->route($request->user()->role === 'midwife' ? 'midwife.children.show' : 'admin.children.show', $child->child_id)->with('success', 'Child details updated successfully.');
    }

    /**
     * Remove child record.
     */
    public function destroy(int $id)
    {
        $child = Child::findOrFail($id);
        $this->authorize('delete', $child);
        $child->delete();

        return redirect()->route('admin.children.index')->with('success', 'Child record removed successfully.');
    }
}
