<?php

namespace App\Http\Controllers;

use App\Models\Area;
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
    

    public function index(Request $request)
    {
        $this->authorize('viewAny', Mother::class);

        $user = Auth::user();
        $search = $request->query('search');
        $areaFilter = $request->query('area_id');

        $allAreas = Area::all();

        $areasQuery = Area::with(['midwives']);
        if ($areaFilter) {
            $areasQuery->where('area_id', $areaFilter);
        }

        $areas = $areasQuery->get()->map(function ($area) use ($search, $user) {
            $midwifeIds = $area->midwives->pluck('midwife_id')->filter()->toArray();

            $mothersQuery = Mother::with(['pregnancyHistories', 'midwife.area'])
                ->where(function ($q) use ($midwifeIds) {
                    if (!empty($midwifeIds)) {
                        $q->whereIn('midwife_id', $midwifeIds);
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                });

            if ($search) {
                $mothersQuery->where(function ($sq) use ($search) {
                    $sq->where('mother_name', 'like', "%{$search}%")
                       ->orWhere('phone_no', 'like', "%{$search}%")
                       ->orWhere('address', 'like', "%{$search}%");
                });
            }

            if ($user && $user->role === 'midwife' && $user->midwife) {
                if ($user->midwife->area_id != $area->area_id) {
                    $mothersQuery->where('midwife_id', $user->midwife->midwife_id);
                }
            }

            $area->filtered_mothers = $mothersQuery->orderBy('mother_id', 'desc')->get();
            return $area;
        });

        $unassignedMothers = collect();
        if ($user && in_array($user->role, ['admin', 'provider'])) {
            $unassignedQuery = Mother::with(['pregnancyHistories', 'midwife.area'])
                ->where(function ($q) {
                    $q->whereNull('midwife_id')
                      ->orWhereDoesntHave('midwife');
                });
            if ($search) {
                $unassignedQuery->where(function ($sq) use ($search) {
                    $sq->where('mother_name', 'like', "%{$search}%")
                       ->orWhere('phone_no', 'like', "%{$search}%")
                       ->orWhere('address', 'like', "%{$search}%");
                });
            }
            if (!$areaFilter) {
                $unassignedMothers = $unassignedQuery->orderBy('mother_id', 'desc')->get();
            }
        }

        $totalMothers = Mother::count();
        $totalMidwives = Midwife::count();

        $allMothersList = Mother::with(['midwife.area'])->get()->map(function ($m) use ($user) {
            $prefix = ($user && $user->role === 'midwife') ? 'midwife.' : 'admin.';
            return [
                'id' => $m->mother_id,
                'name' => $m->mother_name,
                'phone' => $m->phone_no ?? '',
                'area' => $m->midwife?->area?->area_name ?? 'General',
                'midwife' => $m->midwife?->midwife_name ?? '',
                'url' => route($prefix . 'mothers.show', $m->mother_id),
            ];
        });

        return view('mothers.index', compact('user', 'areas', 'unassignedMothers', 'search', 'totalMothers', 'totalMidwives', 'allAreas', 'areaFilter', 'allMothersList'));
    }

    public function create()
    {
        $this->authorize('create', Mother::class);

        $user = Auth::user();
        $areas = Area::with('midwives')->get();
        $midwives = Midwife::with('area')->get();
        return view('mothers.create', compact('user', 'areas', 'midwives'));
    }

    

    public function store(Request $request)
    {
        $this->authorize('create', Mother::class);

        $validated = $request->validate([
            
            'mother_name'        => 'required|string|max:255',
            'phone_no'           => 'nullable|string|max:20',
            'midwife_id'         => 'required|exists:midwives,midwife_id',
            'date_of_birth'      => 'nullable|date|before:today',
            'height'             => 'nullable|numeric|min:50|max:250',
            'weight'             => 'nullable|numeric|min:20|max:300',
            'blood_pressure'     => 'nullable|string|max:50',
            'husband_name'       => 'nullable|string|max:255',
            'husband_occupation' => 'nullable|string|max:255',
            'address'            => 'nullable|string|max:500',

            
            'no_living_children'                   => 'nullable|integer|min:0',
            'age_of_youngest_child'                => 'nullable|integer|min:0',
            'last_menstrual_period'                => 'nullable|date',
            'expected_date_of_delivery'            => 'nullable|date',
            'date_confirmed_by_US'                 => 'nullable|date',
            'no_of_weeks_pregnant_at_registration' => 'nullable|integer|min:0|max:45',
            'first_fetal_movements_date'           => 'nullable|date',

            
            'diabetes'               => 'nullable|string|max:255',
            'high_blood_pressure'    => 'nullable|string|max:255',
            'blood_related_diseases' => 'nullable|string|max:255',
            'other_health'           => 'nullable|string|max:1000',

            
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
            
            $mother = Mother::create([
                'mother_name'        => $validated['mother_name'],
                'phone_no'           => $validated['phone_no'] ?? null,
                'midwife_id'         => $validated['midwife_id'],
                'date_of_birth'      => $validated['date_of_birth'] ?? null,
                'height'             => $validated['height'] ?? null,
                'weight'             => $validated['weight'] ?? null,
                'blood_pressure'     => $validated['blood_pressure'] ?? null,
                'husband_name'       => $validated['husband_name'] ?? null,
                'husband_occupation' => $validated['husband_occupation'] ?? null,
                'address'            => $validated['address'] ?? null,
            ]);

            
            PregnancyHistory::create([
                'mother_id'                            => $mother->mother_id,
                'no_living_children'                   => $validated['no_living_children'] ?? 0,
                'age_of_youngest_child'                => $validated['age_of_youngest_child'] ?? null,
                'last_menstrual_period'                => $validated['last_menstrual_period'] ?? null,
                'expected_date_of_delivery'            => $validated['expected_date_of_delivery'] ?? null,
                'date_confirmed_by_US'                 => $validated['date_confirmed_by_US'] ?? null,
                'no_of_weeks_pregnant_at_registration' => $validated['no_of_weeks_pregnant_at_registration'] ?? null,
                'first_fetal_movements_date'           => $validated['first_fetal_movements_date'] ?? null,
                'is_delivered'                         => false,
            ]);

            
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

    

    public function edit($id)
    {
        $mother = Mother::with([
            'pregnancyHistories',
            'previousPregnancyHistories',
            'familyHealthHistory',
        ])->findOrFail($id);

        $this->authorize('update', $mother);

        $user = Auth::user();

        $areas = Area::with('midwives')->get();
        $midwives = Midwife::with('area')->get();
        $activePregnancy = $mother->activePregnancy();
        $latestPregnancy = $mother->currentPregnancy();

        return view('mothers.edit', compact('user', 'mother', 'areas', 'midwives', 'activePregnancy', 'latestPregnancy'));
    }

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
            'blood_pressure'     => 'nullable|string|max:50',
            'husband_name'       => 'nullable|string|max:255',
            'husband_occupation' => 'nullable|string|max:255',
            'address'            => 'nullable|string|max:500',

            // Current / Next Pregnancy Details
            'is_new_pregnancy'                     => 'nullable|boolean',
            'no_living_children'                   => 'nullable|integer|min:0',
            'age_of_youngest_child'                => 'nullable|integer|min:0',
            'last_menstrual_period'                => 'nullable|date',
            'expected_date_of_delivery'            => 'nullable|date',
            'date_confirmed_by_US'                 => 'nullable|date',
            'no_of_weeks_pregnant_at_registration' => 'nullable|integer|min:0|max:45',
            'first_fetal_movements_date'           => 'nullable|date',
        ]);

        DB::transaction(function () use ($mother, $validated, $request) {
            $mother->update([
                'mother_name'        => $validated['mother_name'],
                'phone_no'           => $validated['phone_no'] ?? null,
                'midwife_id'         => $validated['midwife_id'],
                'date_of_birth'      => $validated['date_of_birth'] ?? null,
                'height'             => $validated['height'] ?? null,
                'weight'             => $validated['weight'] ?? null,
                'blood_pressure'     => $validated['blood_pressure'] ?? null,
                'husband_name'       => $validated['husband_name'] ?? null,
                'husband_occupation' => $validated['husband_occupation'] ?? null,
                'address'            => $validated['address'] ?? null,
            ]);

            $hasPregnancyData = isset($validated['no_living_children'])
                || !empty($validated['last_menstrual_period'])
                || !empty($validated['expected_date_of_delivery'])
                || !empty($validated['no_of_weeks_pregnant_at_registration']);

            if ($hasPregnancyData) {
                $activePregnancy = $mother->activePregnancy();
                $isNewPregnancy = $request->boolean('is_new_pregnancy');

                if ($isNewPregnancy || !$activePregnancy) {
                    if ($activePregnancy) {
                        $activePregnancy->is_delivered = true;
                        $activePregnancy->delivery_date = $activePregnancy->delivery_date ?? now();
                        $activePregnancy->save();

                        $alreadyRecorded = PreviousPregnancyHistory::where('mother_id', $mother->mother_id)
                            ->where('date_of_birth', $activePregnancy->delivery_date)
                            ->exists();

                        if (!$alreadyRecorded) {
                            $count = $mother->previousPregnancyHistories()->count() + 1;
                            $ordinal = match($count) {
                                1 => '1st Pregnancy',
                                2 => '2nd Pregnancy',
                                3 => '3rd Pregnancy',
                                default => "{$count}th Pregnancy",
                            };

                            PreviousPregnancyHistory::create([
                                'mother_id'          => $mother->mother_id,
                                'date_of_birth'      => $activePregnancy->delivery_date,
                                'birth_weight'       => null,
                                'gender'             => null,
                                'which_pregnancy'    => $ordinal,
                                'results'            => 'Completed Delivery',
                                'place_of_pregnancy' => 'Hospital/Clinic',
                            ]);
                        }
                    }

                    PregnancyHistory::create([
                        'mother_id'                            => $mother->mother_id,
                        'no_living_children'                   => $validated['no_living_children'] ?? 0,
                        'age_of_youngest_child'                => $validated['age_of_youngest_child'] ?? null,
                        'last_menstrual_period'                => $validated['last_menstrual_period'] ?? null,
                        'expected_date_of_delivery'            => $validated['expected_date_of_delivery'] ?? null,
                        'date_confirmed_by_US'                 => $validated['date_confirmed_by_US'] ?? null,
                        'no_of_weeks_pregnant_at_registration' => $validated['no_of_weeks_pregnant_at_registration'] ?? null,
                        'first_fetal_movements_date'           => $validated['first_fetal_movements_date'] ?? null,
                        'is_delivered'                         => false,
                    ]);
                } else {
                    $activePregnancy->update([
                        'no_living_children'                   => $validated['no_living_children'] ?? $activePregnancy->no_living_children,
                        'age_of_youngest_child'                => $validated['age_of_youngest_child'] ?? $activePregnancy->age_of_youngest_child,
                        'last_menstrual_period'                => $validated['last_menstrual_period'] ?? $activePregnancy->last_menstrual_period,
                        'expected_date_of_delivery'            => $validated['expected_date_of_delivery'] ?? $activePregnancy->expected_date_of_delivery,
                        'date_confirmed_by_US'                 => $validated['date_confirmed_by_US'] ?? $activePregnancy->date_confirmed_by_US,
                        'no_of_weeks_pregnant_at_registration' => $validated['no_of_weeks_pregnant_at_registration'] ?? $activePregnancy->no_of_weeks_pregnant_at_registration,
                        'first_fetal_movements_date'           => $validated['first_fetal_movements_date'] ?? $activePregnancy->first_fetal_movements_date,
                    ]);
                }
            }
        });

        return redirect()->route($request->user()->role === 'midwife' ? 'midwife.mothers.show' : 'admin.mothers.show', $mother->mother_id)
            ->with('success', 'Mother record updated successfully!');
    }

    public function recordDelivery(Request $request, $id)
    {
        $mother = Mother::findOrFail($id);
        $this->authorize('update', $mother);

        $validated = $request->validate([
            'delivery_date'      => 'required|date|before_or_equal:today',
            'gender'             => 'nullable|in:Male,Female',
            'birth_weight'       => 'nullable|numeric|min:0.1|max:10',
            'results'            => 'nullable|string|max:255',
            'place_of_pregnancy' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($mother, $validated) {
            $active = $mother->activePregnancy()
                ?? $mother->pregnancyHistories()->latest()->first();

            if ($active) {
                $active->is_delivered = true;
                $active->delivery_date = $validated['delivery_date'];
                $active->save();
            }

            $count = $mother->previousPregnancyHistories()->count() + 1;
            $ordinal = match($count) {
                1 => '1st Pregnancy',
                2 => '2nd Pregnancy',
                3 => '3rd Pregnancy',
                default => "{$count}th Pregnancy",
            };

            PreviousPregnancyHistory::create([
                'mother_id'          => $mother->mother_id,
                'date_of_birth'      => $validated['delivery_date'],
                'birth_weight'       => $validated['birth_weight'] ?? null,
                'gender'             => $validated['gender'] ?? null,
                'which_pregnancy'    => $ordinal,
                'results'            => $validated['results'] ?? 'Live Birth',
                'place_of_pregnancy' => $validated['place_of_pregnancy'] ?? 'Hospital/Clinic',
            ]);
        });

        $user = Auth::user();
        $redirectRoute = ($user && $user->role === 'midwife') ? 'midwife.children.create' : 'admin.children.create';

        return redirect()->route($redirectRoute, [
            'mother_id'     => $mother->mother_id,
            'date_of_birth' => $validated['delivery_date'],
            'gender'        => $validated['gender'] ?? '',
            'birth_weight'  => $validated['birth_weight'] ?? '',
        ])->with('success', 'Delivery recorded successfully! Please complete registration for the newborn child.');
    }

    

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

    public function destroy($id)
    {
        $mother = Mother::findOrFail($id);
        $this->authorize('delete', $mother);

        $motherName = $mother->mother_name;

        DB::transaction(function () use ($mother) {
            // Delete associated login user account if one exists for this mother
            if ($mother->user_id) {
                $user = $mother->user;
                if ($user && $user->role === 'mother') {
                    $user->delete();
                }
            }

            // Explicitly delete all associated children and their health records
            foreach ($mother->children as $child) {
                \App\Models\Immunization::where('child_id', $child->child_id)->delete();
                \App\Models\Attendance::where('child_id', $child->child_id)->delete();
                $child->delete();
            }

            // Clean up mother clinical records
            PreviousPregnancyHistory::where('mother_id', $mother->mother_id)->delete();
            PregnancyHistory::where('mother_id', $mother->mother_id)->delete();
            FamilyHealthHistory::where('mother_id', $mother->mother_id)->delete();
            \App\Models\TestDone::where('mother_id', $mother->mother_id)->delete();
            \App\Models\TriposhaBook::where('mother_id', $mother->mother_id)->delete();
            \App\Models\Attendance::where('mother_id', $mother->mother_id)->delete();

            $mother->delete();
        });

        $user = Auth::user();
        $redirectRoute = ($user && $user->role === 'midwife') ? 'midwife.mothers.index' : 'admin.mothers.index';

        return redirect()->route($redirectRoute)
            ->with('success', "Mother record for {$motherName} (ID #{$id}) has been permanently deleted.");
    }
}
