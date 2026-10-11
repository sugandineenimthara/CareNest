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

class ImmunizationController extends Controller
{
    /**
     * Display the immunization dashboard with separate tabs for child and mother vaccination.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Immunization::class);

        $user = Auth::user();
        $tab = $request->query('tab', 'children'); // 'children' or 'mothers'
        $search = $request->query('search');
        $areaFilter = $request->query('area_id');

        $allAreas = Area::all();
        $midwives = Midwife::with('area')->get();

        // 1. CHILDREN VACCINATION DATA
        $childrenQuery = Child::with(['mother', 'midwife.area', 'area', 'immunizations']);

        if ($areaFilter) {
            $childrenQuery->where(function ($q) use ($areaFilter) {
                $q->where('area_id', $areaFilter)
                  ->orWhereHas('midwife', fn ($mq) => $mq->where('area_id', $areaFilter));
            });
        }

        if ($user->role === 'mother') {
            if (!$user->mother) abort(403, 'Your mother profile is not linked or incomplete. Please contact the administrator.');
            $childrenQuery->where('mother_id', $user->mother->mother_id);
        } elseif ($user->role === 'midwife' && $user->midwife) {
            $childrenQuery->where(function($q) use ($user) {
                $q->where('midwife_id', $user->midwife->midwife_id)
                  ->orWhereHas('mother', fn ($mq) => $mq->where('midwife_id', $user->midwife->midwife_id));
            });
        }

        if ($search && $tab === 'children') {
            $childrenQuery->where(function ($q) use ($search) {
                $q->where('child_id', 'like', "%{$search}%")
                  ->orWhere('child_name', 'like', "%{$search}%")
                  ->orWhereHas('mother', fn ($mq) => $mq->where('mother_name', 'like', "%{$search}%")->orWhere('mother_id', 'like', "%{$search}%"));
            });
        }

        $children = $childrenQuery->orderBy('date_of_birth', 'desc')->get();

        // Standard Schedule definition
        $standardSchedule = Child::getVaccinationSchedule();

        // Compute vaccination status summary for each child
        $childrenWithSchedule = $children->map(function ($child) use ($standardSchedule) {
            $dob = Carbon::parse($child->date_of_birth);
            $completedCount = 0;
            $overdueCount = 0;
            $dueNowCount = 0;

            $items = [];
            foreach ($standardSchedule as $vac) {
                $recorded = $child->immunizations->first(function ($imm) use ($vac) {
                    $prefix = explode(' ', $vac['vaccine_name'])[0];
                    return stripos($imm->vaccine_name, $prefix) !== false;
                });

                $dueDate = $dob->copy()->addMonths($vac['months_due']);

                if ($recorded) {
                    $status = 'Completed';
                    $completedCount++;
                } elseif (now()->greaterThanOrEqualTo($dueDate->copy()->addMonth())) {
                    $status = 'Overdue';
                    $overdueCount++;
                } elseif (now()->greaterThanOrEqualTo($dueDate)) {
                    $status = 'Due Now';
                    $dueNowCount++;
                } else {
                    $status = 'Upcoming';
                }

                $items[] = [
                    'vaccine' => $vac,
                    'dueDate' => $dueDate,
                    'status' => $status,
                    'record' => $recorded,
                ];
            }

            $child->schedule_items = $items;
            $child->completed_vaccines = $completedCount;
            $child->total_vaccines = count($standardSchedule);
            $child->overdue_vaccines = $overdueCount;
            $child->due_now_vaccines = $dueNowCount;
            $child->progress_percent = round(($completedCount / count($standardSchedule)) * 100);

            return $child;
        });

        // Recent child immunizations list
        $recentChildImmunizations = Immunization::with(['child.mother', 'midwife'])
            ->whereNotNull('child_id')
            ->orderBy('immunization_date', 'desc')
            ->limit(20)
            ->get();

        // 2. MOTHER VACCINATION DATA (TETANUS)
        $mothersQuery = Mother::with(['midwife.area', 'previousPregnancyHistories', 'immunizations']);

        if ($areaFilter) {
            $mothersQuery->whereHas('midwife', fn ($mq) => $mq->where('area_id', $areaFilter));
        }

        if ($user->role === 'mother') {
            if (!$user->mother) abort(403, 'Your mother profile is not linked or incomplete. Please contact the administrator.');
            $mothersQuery->where('mother_id', $user->mother->mother_id);
        } elseif ($user->role === 'midwife' && $user->midwife) {
            $mothersQuery->where('midwife_id', $user->midwife->midwife_id);
        }

        if ($search && $tab === 'mothers') {
            $mothersQuery->where(function ($q) use ($search) {
                $q->where('mother_name', 'like', "%{$search}%")
                  ->orWhere('mother_id', 'like', "%{$search}%")
                  ->orWhere('phone_no', 'like', "%{$search}%");
            });
        }

        $allMothers = $mothersQuery->get()->map(function ($mother) {
            $pregNo = $mother->pregnancy_number;
            $isSafe = $pregNo > 4;

            // Check if Tetanus / TT vaccine is recorded for this mother
            $tetanusRecord = $mother->immunizations->first(function ($imm) {
                return stripos($imm->vaccine_name, 'tetanus') !== false || stripos($imm->vaccine_name, 'tt') !== false;
            });

            $mother->current_preg_no = $pregNo;
            $mother->is_tetanus_safe = $isSafe;
            $mother->tetanus_record = $tetanusRecord;

            if ($isSafe) {
                $mother->tetanus_status = 'Safe';
                $mother->status_label = 'Safe from Tetanus (>4th Pregnancy)';
            } elseif ($tetanusRecord) {
                $mother->tetanus_status = 'Vaccinated';
                $mother->status_label = 'Vaccinated (Batch: ' . $tetanusRecord->batch_no . ')';
            } else {
                $mother->tetanus_status = 'Due';
                $mother->status_label = 'Tetanus Vaccination Required';
            }

            return $mother;
        });

        // Recent mother immunizations list
        $recentMotherImmunizations = Immunization::with(['mother.midwife', 'midwife'])
            ->whereNotNull('mother_id')
            ->orderBy('immunization_date', 'desc')
            ->limit(20)
            ->get();

        // Summary counts
        $totalChildVaccinesGiven = Immunization::whereNotNull('child_id')->count();
        $totalMotherVaccinesGiven = Immunization::whereNotNull('mother_id')->count();
        $mothersSafeCount = $allMothers->where('is_tetanus_safe', true)->count();
        $mothersDueCount = $allMothers->where('tetanus_status', 'Due')->count();

        return view('immunizations.index', compact(
            'user',
            'tab',
            'search',
            'areaFilter',
            'allAreas',
            'midwives',
            'childrenWithSchedule',
            'recentChildImmunizations',
            'allMothers',
            'recentMotherImmunizations',
            'standardSchedule',
            'totalChildVaccinesGiven',
            'totalMotherVaccinesGiven',
            'mothersSafeCount',
            'mothersDueCount'
        ));
    }

    /**
     * Store a child immunization dose with batch number and date vaccinated.
     */
    public function storeChildVaccine(Request $request)
    {
        $this->authorize('create', Immunization::class);

        $validated = $request->validate([
            'child_id'          => 'required|exists:children,child_id',
            'vaccine_name'      => 'required|string|max:100',
            'batch_no'          => 'required|string|max:100',
            'immunization_date' => 'required|date|before_or_equal:today',
            'dose'              => 'nullable|string|max:100',
            'midwife_id'        => 'nullable|exists:midwives,midwife_id',
            'expiry_date'       => 'nullable|date|after_or_equal:immunization_date',
            'remarks'           => 'nullable|string|max:500',
        ]);

        $child = Child::with('midwife')->findOrFail($validated['child_id']);
        $midwifeId = $validated['midwife_id'] ?? ($child->midwife_id ?? Midwife::first()->midwife_id);

        $immunization = Immunization::create([
            'child_id'          => $child->child_id,
            'mother_id'         => null,
            'midwife_id'        => $midwifeId,
            'vaccine_name'      => $validated['vaccine_name'],
            'batch_no'          => $validated['batch_no'],
            'immunization_date' => $validated['immunization_date'],
            'dose'              => $validated['dose'] ?? null,
            'age'               => $child->age ?? null,
            'expiry_date'       => $validated['expiry_date'] ?? null,
            'status'            => 'Completed',
            'remarks'           => $validated['remarks'] ?? null,
        ]);

        // If BCG, update child flag
        if (stripos($validated['vaccine_name'], 'bcg') !== false) {
            $child->update([
                'bcg_vaccinated_at_birth' => true,
                'bcg_batch_no'            => $validated['batch_no'],
                'bcg_vaccinated_date'     => $validated['immunization_date'],
            ]);
        }

        return redirect()->back()->with('success', 'Vaccine ' . $validated['vaccine_name'] . ' (Batch #' . $validated['batch_no'] . ') recorded successfully for ' . $child->display_name . '!');
    }

    /**
     * Store a mother Tetanus immunization with batch number and date vaccinated.
     */
    public function storeMotherVaccine(Request $request)
    {
        $this->authorize('create', Immunization::class);

        $validated = $request->validate([
            'mother_id'         => 'required|exists:mothers,mother_id',
            'batch_no'          => 'required|string|max:100',
            'immunization_date' => 'required|date|before_or_equal:today',
            'dose'              => 'nullable|string|max:100',
            'midwife_id'        => 'nullable|exists:midwives,midwife_id',
            'expiry_date'       => 'nullable|date|after_or_equal:immunization_date',
            'remarks'           => 'nullable|string|max:500',
        ]);

        $mother = Mother::with('midwife')->findOrFail($validated['mother_id']);
        $midwifeId = $validated['midwife_id'] ?? ($mother->midwife_id ?? Midwife::first()->midwife_id);

        $doseName = $validated['dose'] ?? ('Pregnancy #' . $mother->pregnancy_number . ' Tetanus Dose');

        Immunization::create([
            'child_id'          => null,
            'mother_id'         => $mother->mother_id,
            'midwife_id'        => $midwifeId,
            'vaccine_name'      => 'Tetanus Toxoid (TT)',
            'batch_no'          => $validated['batch_no'],
            'immunization_date' => $validated['immunization_date'],
            'dose'              => $doseName,
            'age'               => 'Adult (Mother)',
            'expiry_date'       => $validated['expiry_date'] ?? null,
            'status'            => 'Completed',
            'remarks'           => $validated['remarks'] ?? ('Administered for Pregnancy #' . $mother->pregnancy_number),
        ]);

        return redirect()->back()->with('success', 'Tetanus vaccine (Batch #' . $validated['batch_no'] . ') recorded successfully for Mother ' . $mother->mother_name . ' (Pregnancy #' . $mother->pregnancy_number . ')!');
    }
}
