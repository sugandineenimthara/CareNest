<?php

namespace App\Http\Controllers;

use App\Models\Immunization;
use App\Models\Midwife;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class VaccineReportController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        abort_unless(
            $user && in_array(
                $user->role,
                ['provider', 'admin', 'midwife', 'mother'],
                true
            ),
            403
        );

        if ($user->role === 'mother') {
            abort_unless(
                $user->mother,
                403,
                'Your mother profile is not linked.'
            );
        }

        if ($user->role === 'midwife') {
            abort_unless(
                $user->midwife,
                403,
                'Your midwife profile is not linked.'
            );
        }

        $validated = $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => [
                'nullable',
                'date',
                'after_or_equal:from_date',
            ],
            'midwife_id' => [
                'nullable',
                'integer',
                'exists:midwives,midwife_id',
            ],
        ]);

        $fromDate = $validated['from_date'] ?? null;
        $toDate = $validated['to_date'] ?? null;

        // Apply the logged-in user's access restrictions first.
        $query = $this->scopedQuery($user);

        $selectedMidwifeId = null;

        if (in_array($user->role, ['admin', 'provider'], true)) {
            $selectedMidwifeId = $validated['midwife_id'] ?? null;
        } elseif ($user->role === 'midwife') {
            $selectedMidwifeId = $user->midwife->midwife_id;
        }

        // Filter records for the selected midwife.
        if ($selectedMidwifeId !== null) {
            $midwifeId = $selectedMidwifeId;

            $query->where(function (Builder $builder) use ($midwifeId) {
                $builder->where('midwife_id', $midwifeId)
                    ->orWhereHas('child', function (Builder $child) use ($midwifeId) {
                        $child->where('midwife_id', $midwifeId)
                            ->orWhereHas('mother', function (Builder $mother) use ($midwifeId) {
                                $mother->where('midwife_id', $midwifeId);
                            });
                    })
                    ->orWhereHas('mother', function (Builder $mother) use ($midwifeId) {
                        $mother->where('midwife_id', $midwifeId);
                    });
            });
        }

        if ($fromDate) {
            $query->whereDate('immunization_date', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('immunization_date', '<=', $toDate);
        }

        $relations = [
            'child.mother',
            'child.area',
            'child.midwife.area',
            'mother.midwife.area',
            'midwife.area',
        ];

        $summaryRecords = (clone $query)
            ->with($relations)
            ->orderByDesc('immunization_date')
            ->orderByDesc('immunization_id')
            ->get();

        $totalVaccinations = $summaryRecords->count();

        $totalMothers = $summaryRecords
            ->map(fn ($record) => $record->mother_id
                ?? $record->child?->mother_id)
            ->filter()
            ->unique()
            ->count();

        $totalChildren = $summaryRecords
            ->pluck('child_id')
            ->filter()
            ->unique()
            ->count();

        // Monthly vaccination trend.
        $monthlySummary = $summaryRecords
            ->filter(fn ($record) => $record->immunization_date !== null)
            ->groupBy(
                fn ($record) => $record->immunization_date->format('Y-m')
            )
            ->map(function ($items, $month) {
                return [
                    'month' => $month,
                    'label' => $items->first()
                        ->immunization_date
                        ->format('M Y'),
                    'total' => $items->count(),
                ];
            })
            ->sortKeys()
            ->values();

        // Mother-wise summary.
        $motherSummary = $this->summarize(
            $summaryRecords,
            function ($record) {
                $mother = $record->mother
                    ?? $record->child?->mother;

                return [
                    'id' => $mother?->mother_id ?? 'unassigned',
                    'name' => $mother?->mother_name
                        ?? 'Mother not assigned',
                ];
            }
        );

        // Child-wise summary.
        $childSummary = $this->summarize(
            $summaryRecords,
            function ($record) {
                $child = $record->child;

                return [
                    'id' => $child?->child_id ?? 'unassigned',
                    'name' => $child?->display_name
                        ?? $child?->child_name
                        ?? ($child
                            ? 'Child #' . $child->child_id
                            : 'Child not assigned'),
                ];
            }
        );

        // Midwife-wise summary.
        $midwifeSummary = $this->summarize(
            $summaryRecords,
            function ($record) {
                $midwife = $record->midwife
                    ?? $record->child?->midwife
                    ?? $record->mother?->midwife
                    ?? $record->child?->mother?->midwife;

                return [
                    'id' => $midwife?->midwife_id ?? 'unassigned',
                    'name' => $midwife?->midwife_name
                        ?? 'Midwife not assigned',
                ];
            }
        );

        // Area-wise summary.
        $areaSummary = $this->summarize(
            $summaryRecords,
            function ($record) {
                $area = $record->child?->area
                    ?? $record->child?->midwife?->area
                    ?? $record->mother?->midwife?->area
                    ?? $record->child?->mother?->midwife?->area
                    ?? $record->midwife?->area;

                return [
                    'id' => $area?->area_id ?? 'unassigned',
                    'name' => $area?->area_name ?? 'Area not assigned',
                ];
            }
        );

        $midwives = in_array($user->role, ['admin', 'provider'], true)
            ? Midwife::query()->orderBy('midwife_name')->get()
            : collect();

        $selectedMidwife = $selectedMidwifeId !== null
            ? Midwife::query()
                ->where('midwife_id', $selectedMidwifeId)
                ->first()
            : null;

        $dashboardRoute = in_array(
            $user->role,
            ['admin', 'provider'],
            true
        )
            ? 'admin.dashboard'
            : $user->role . '.dashboard';

        return view('vaccine-reports.index', [
            'user' => $user,
            'role' => $user->role,
            'dashboardRoute' => $dashboardRoute,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'selectedMidwifeId' => $selectedMidwifeId,
            'selectedMidwife' => $selectedMidwife,
            'midwives' => $midwives,
            'totalVaccinations' => $totalVaccinations,
            'totalMothers' => $totalMothers,
            'totalChildren' => $totalChildren,
            'monthlySummary' => $monthlySummary,
            'motherSummary' => $motherSummary,
            'childSummary' => $childSummary,
            'midwifeSummary' => $midwifeSummary,
            'areaSummary' => $areaSummary,
        ]);
    }

    private function summarize(
        Collection $records,
        callable $resolver
    ): Collection {
        return $records
            ->groupBy(function ($record) use ($resolver) {
                $item = $resolver($record);

                return $item['id'];
            })
            ->map(function ($items) use ($resolver) {
                $item = $resolver($items->first());

                return [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'total' => $items->count(),
                ];
            })
            ->sortByDesc('total')
            ->values();
    }

    private function scopedQuery($user): Builder
    {
        $query = Immunization::query();

        if ($user->role === 'mother') {
            $motherId = $user->mother->mother_id;

            $query->where(function (Builder $builder) use ($motherId) {
                $builder->where('mother_id', $motherId)
                    ->orWhereHas('child', function (Builder $child) use ($motherId) {
                        $child->where('mother_id', $motherId);
                    });
            });
        } elseif ($user->role === 'midwife') {
            $midwifeId = $user->midwife->midwife_id;

            $query->where(function (Builder $builder) use ($midwifeId) {
                $builder->where('midwife_id', $midwifeId)
                    ->orWhereHas('child', function (Builder $child) use ($midwifeId) {
                        $child->where('midwife_id', $midwifeId)
                            ->orWhereHas('mother', function (Builder $mother) use ($midwifeId) {
                                $mother->where('midwife_id', $midwifeId);
                            });
                    })
                    ->orWhereHas('mother', function (Builder $mother) use ($midwifeId) {
                        $mother->where('midwife_id', $midwifeId);
                    });
            });
        }

        return $query;
    }
}