<?php

namespace App\Http\Controllers;

use App\Models\FamilyHealthHistory;
use App\Models\Midwife;
use App\Models\Mother;
use App\Models\PregnancyHistory;
use App\Models\PreviousPregnancyHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MotherController extends Controller
{
    /**
     * Display all mothers grouped by midwife.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Mother::class);

        $user = Auth::user();
        $search = $request->query('search');

        $midwives = Midwife::with(['area', 'mothers' => function ($q) use ($search) {
            $q->with(['pregnancyHistories']);
            if ($search) {
                $q->where(function ($sq) use ($search) {
                    $sq->where('mother_name', 'like', "%{$search}%")
                       ->orWhere('phone_no', 'like', "%{$search}%")
                       ->orWhere('address', 'like', "%{$search}%");
                });
            }
        }])->get();

        $totalMothers = Mother::count();
        $totalMidwives = Midwife::count();

        return view('mothers.index', compact('user', 'midwives', 'search', 'totalMothers', 'totalMidwives'));
    }

    /**
     * Show the registration form for a new mother.
     */
    public function create()
    {
        $this->authorize('create', Mother::class);

        $user = Auth::user();
        $midwives = Midwife::with('area')->get();
        return view('mothers.create', compact('user', 'midwives'));
    }

    /**
     * Store a new mother with pregnancy history and previous pregnancy records.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Mother::class);

        $validated = $request->validate([
            // Mother details
            'mother_name'        => 'required|string|max:255',
            'phone_no'           => 'nullable|string|max:20',
            'midwife_id'         => 'required|exists:midwives,midwife_id',
            'date_of_birth'      => 'nullable|date|before:today',
            'height'             => 'nullable|numeric|min:50|max:250',
            'weight'             => 'nullable|numeric|min:20|max:300',
            'husband_name'       => 'nullable|string|max:255',
            'husband_occupation' => 'nullable|string|max:255',
            'address'            => 'nullable|string|max:500',

            // Current pregnancy history
            'no_living_children'                   => 'nullable|integer|min:0',
            'age_of_youngest_child'                => 'nullable|integer|min:0',
            'last_menstrual_period'                => 'nullable|date',
            'expected_date_of_delivery'            => 'nullable|date',
            'date_confirmed_by_US'                 => 'nullable|date',
            'no_of_weeks_pregnant_at_registration' => 'nullable|integer|min:0|max:45',
            'first_fetal_movements_date'           => 'nullable|date',

            // Family health history
            'diabetes'               => 'nullable|string|max:255',
            'high_blood_pressure'    => 'nullable|string|max:255',
            'blood_related_diseases' => 'nullable|string|max:255',
            'other_health'           => 'nullable|string|max:1000',

            // Previous pregnancy history (array)
            'prev_pregnancy'                             => 'nullable|array',
            'prev_pregnancy.*.date_of_birth'             => 'nullable|date',
            'prev_pregnancy.*.birth_weight'              => 'nullable|numeric|min:0|max:10',
            'prev_pregnancy.*.gender'                    => 'nullable|in:Male,Female',
            'prev_pregnancy.*.which_pregnancy'           => 'nullable|string|max:100',
            'prev_pregnancy.*.results'                   => 'nullable|string|max:255',
            'prev_pregnancy.*.place_of_pregnancy'        => 'nullable|string|max:255',
            'prev_pregnancy.*.rubella_vaccinated'        => 'nullable',
            'prev_pregnancy.*.folic_acid_vaccinated'     => 'nullable',
            'prev_pregnancy.*.infertility'               => 'nullable',
            'prev_pregnancy.*.blood_relation_marriage'   => 'nullable',
        ]);

        DB::transaction(function () use ($validated) {
            // 1. Create Mother
            $mother = Mother::create([
                'mother_name'        => $validated['mother_name'],
                'phone_no'           => $validated['phone_no'] ?? null,
                'midwife_id'         => $validated['midwife_id'],
                'date_of_birth'      => $validated['date_of_birth'] ?? null,
                'height'             => $validated['height'] ?? null,
                'weight'             => $validated['weight'] ?? null,
                'husband_name'       => $validated['husband_name'] ?? null,
                'husband_occupation' => $validated['husband_occupation'] ?? null,
                'address'            => $validated['address'] ?? null,
            ]);

            // 2. Create current Pregnancy History
            $hasPregnancyData = isset($validated['no_living_children'])
                || !empty($validated['last_menstrual_period'])
                || !empty($validated['expected_date_of_delivery'])
                || !empty($validated['no_of_weeks_pregnant_at_registration']);

            if ($hasPregnancyData) {
                PregnancyHistory::create([
                    'mother_id'                            => $mother->mother_id,
                    'no_living_children'                   => $validated['no_living_children'] ?? 0,
                    'age_of_youngest_child'                => $validated['age_of_youngest_child'] ?? null,
                    'last_menstrual_period'                => $validated['last_menstrual_period'] ?? null,
                    'expected_date_of_delivery'            => $validated['expected_date_of_delivery'] ?? null,
                    'date_confirmed_by_US'                 => $validated['date_confirmed_by_US'] ?? null,
                    'no_of_weeks_pregnant_at_registration' => $validated['no_of_weeks_pregnant_at_registration'] ?? null,
                    'first_fetal_movements_date'           => $validated['first_fetal_movements_date'] ?? null,
                ]);
            }

            // 3. Create Family Health History
            $hasFamilyHealth = !empty($validated['diabetes'])
                || !empty($validated['high_blood_pressure'])
                || !empty($validated['blood_related_diseases'])
                || !empty($validated['other_health']);

            if ($hasFamilyHealth) {
                FamilyHealthHistory::create([
                    'mother_id'              => $mother->mother_id,
                    'midwife_id'             => $validated['midwife_id'],
                    'diabetes'               => $validated['diabetes'] ?? null,
                    'high_blood_pressure'    => $validated['high_blood_pressure'] ?? null,
                    'blood_related_diseases' => $validated['blood_related_diseases'] ?? null,
                    'other'                  => $validated['other_health'] ?? null,
                ]);
            }

            // 4. Create Previous Pregnancy History records
            if (!empty($validated['prev_pregnancy'])) {
                foreach ($validated['prev_pregnancy'] as $prev) {
                    $hasData = !empty($prev['date_of_birth']) || !empty($prev['birth_weight'])
                        || !empty($prev['gender']) || !empty($prev['which_pregnancy'])
                        || !empty($prev['results']) || !empty($prev['place_of_pregnancy']);

                    if ($hasData) {
                        PreviousPregnancyHistory::create([
                            'mother_id'               => $mother->mother_id,
                            'date_of_birth'           => $prev['date_of_birth'] ?? null,
                            'birth_weight'            => $prev['birth_weight'] ?? null,
                            'gender'                  => $prev['gender'] ?? null,
                            'which_pregnancy'         => $prev['which_pregnancy'] ?? null,
                            'results'                 => $prev['results'] ?? null,
                            'place_of_pregnancy'      => $prev['place_of_pregnancy'] ?? null,
                            'rubella_vaccinated'      => isset($prev['rubella_vaccinated']) ? 1 : 0,
                            'folic_acid_vaccinated'   => isset($prev['folic_acid_vaccinated']) ? 1 : 0,
                            'infertility'             => isset($prev['infertility']) ? 1 : 0,
                            'blood_relation_marriage' => isset($prev['blood_relation_marriage']) ? 1 : 0,
                        ]);
                    }
                }
            }
        });

        return redirect()->route($request->user()->role === 'midwife' ? 'midwife.mothers.index' : 'admin.mothers.index')
            ->with('success', 'Mother registered successfully!');
    }

    /**
     * Display a single mother's profile.
     */
    public function show($id)
    {
        $mother = Mother::with([
            'midwife.area',
            'pregnancyHistories',
            'previousPregnancyHistories',
            'familyHealthHistory',
            'testsDone',
            'triposhaBooks',
            'attendances',
            'children',
        ])->findOrFail($id);

        $this->authorize('view', $mother);

        $user = Auth::user();

        return view('mothers.show', compact('user', 'mother'));
    }

    /**
     * Show edit form for a mother.
     */
    public function edit($id)
    {
        $mother = Mother::with([
            'pregnancyHistories',
            'previousPregnancyHistories',
            'familyHealthHistory',
        ])->findOrFail($id);

        $this->authorize('update', $mother);

        $user = Auth::user();

        $midwives = Midwife::with('area')->get();

        return view('mothers.edit', compact('user', 'mother', 'midwives'));
    }

    /**
     * Update a mother record.
     */
    public function update(Request $request, $id)
    {
        $mother = Mother::findOrFail($id);
        $this->authorize('update', $mother);

        $validated = $request->validate([
            'mother_name'        => 'required|string|max:255',
            'phone_no'           => 'nullable|string|max:20',
            'midwife_id'         => 'required|exists:midwives,midwife_id',
            'date_of_birth'      => 'nullable|date|before:today',
            'height'             => 'nullable|numeric|min:50|max:250',
            'weight'             => 'nullable|numeric|min:20|max:300',
            'husband_name'       => 'nullable|string|max:255',
            'husband_occupation' => 'nullable|string|max:255',
            'address'            => 'nullable|string|max:500',
        ]);

        $mother->update($validated);

        return redirect()->route($request->user()->role === 'midwife' ? 'midwife.mothers.show' : 'admin.mothers.show', $mother->mother_id)
            ->with('success', 'Mother record updated successfully!');
    }

    /**
     * Display mother's own profile.
     */
    public function profile()
    {
        $user = Auth::user();
        $mother = $user->mother;

        if (!$mother) {
            abort(404, 'Mother profile not found.');
        }

        $mother->load([
            'midwife.area',
            'pregnancyHistories',
            'previousPregnancyHistories',
            'familyHealthHistory',
            'testsDone',
            'triposhaBooks',
            'attendances',
            'children',
        ]);

        return view('mothers.show', compact('user', 'mother'));
    }
}
