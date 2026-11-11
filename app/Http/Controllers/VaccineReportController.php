<?php

namespace App\Http\Controllers;

use App\Models\Immunization;
use App\Models\Midwife;
use App\Models\Report;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VaccineReportController extends Controller
{
    protected array $pediatricVaccines = [
        'BCG'                        => ['BCG'],
        'Pentavalent'                => ['Penta', 'Pentavalent'],
        'Oral Polio (OPV)'           => ['Polio', 'OPV'],
        'Inactivated Polio (FITV)'   => ['FITV', 'fIPV', 'IPV'],
        'MMR'                        => ['MMR', 'Measles'],
        'Japanese Encephalitis (JE)' => ['JE', 'Japanese'],
        'DPT'                        => ['DPT'],
        'DT'                         => ['DT'],
    ];

    protected array $motherVaccines = [
        'Tetanus Toxoid (TT)'        => ['Tetanus', 'TT'],
    ];

    public function index(Request $request)
    {
        $user = $request->user();

        abort_unless(
            $user && in_array($user->role, ['provider', 'admin', 'midwife', 'mother'], true),
            403
        );

        if ($user->role === 'midwife') {
            abort_unless($user->midwife, 403, 'Your midwife profile is not linked.');
        }

        if ($user->role === 'mother') {
            abort_unless($user->mother, 403, 'Your mother profile is not linked.');
        }

        $fromDate   = $request->input('from_date') ?? $request->input('date_from');
        $toDate     = $request->input('to_date') ?? $request->input('date_to');
        $clinicType = $request->input('clinic_type');

        if ($clinicType && !in_array($clinicType, ['pediatric', 'mother'], true)) {
            $clinicType = null;
        }

        $reportQuery = Report::query();
        if ($clinicType) {
            $reportQuery->where(function ($q) use ($clinicType) {
                $q->where('clinic_type', $clinicType);
                if ($clinicType === 'pediatric') {
                    $q->orWhere(function ($sq) {
                        $sq->whereNull('clinic_type')
                           ->where('vaccine_used', 'not like', '%tetanus%')
                           ->where('vaccine_used', 'not like', '%TT%');
                    });
                } elseif ($clinicType === 'mother') {
                    $q->orWhere(function ($sq) {
                        $sq->whereNull('clinic_type')
                           ->where(function ($mq) {
                               $mq->where('vaccine_used', 'like', '%tetanus%')
                                  ->orWhere('vaccine_used', 'like', '%TT%');
                           });
                    });
                }
            });
        }
        if ($fromDate) {
            $reportQuery->whereDate('date', '>=', $fromDate);
        }
        if ($toDate) {
            $reportQuery->whereDate('date', '<=', $toDate);
        }
        $reports = $reportQuery->get();

        $immunQuery = Immunization::query();
        if ($clinicType === 'pediatric') {
            $immunQuery->whereNotNull('child_id');
        } elseif ($clinicType === 'mother') {
            $immunQuery->whereNotNull('mother_id');
        }
        if ($fromDate) {
            $immunQuery->whereDate('immunization_date', '>=', $fromDate);
        }
        if ($toDate) {
            $immunQuery->whereDate('immunization_date', '<=', $toDate);
        }
        $immunizations = $immunQuery->get();

        if ($clinicType === 'pediatric') {
            $vaccineDefinitions = $this->pediatricVaccines;
        } elseif ($clinicType === 'mother') {
            $vaccineDefinitions = $this->motherVaccines;
        } else {
            $vaccineDefinitions = array_merge($this->pediatricVaccines, $this->motherVaccines);
        }

        $existingReportVaccines = $reports->pluck('vaccine_used')->filter()->unique();
        foreach ($existingReportVaccines as $customVac) {
            $matched = false;
            foreach ($vaccineDefinitions as $label => $keywords) {
                foreach ($keywords as $kw) {
                    if (stripos($customVac, $kw) !== false) {
                        $matched = true;
                        break 2;
                    }
                }
            }
            if (!$matched) {
                $vaccineDefinitions[$customVac] = [$customVac];
            }
        }

        $existingImmunVaccines = $immunizations->pluck('vaccine_name')->filter()->unique();
        foreach ($existingImmunVaccines as $customVac) {
            $matched = false;
            foreach ($vaccineDefinitions as $label => $keywords) {
                foreach ($keywords as $kw) {
                    if (stripos($customVac, $kw) !== false) {
                        $matched = true;
                        break 2;
                    }
                }
            }
            if (!$matched) {
                $vaccineDefinitions[$customVac] = [$customVac];
            }
        }

        $vaccineRows = collect();

        foreach ($vaccineDefinitions as $label => $keywords) {
            $storageStock = $reports->filter(function ($rep) use ($keywords, $label) {
                if (!$rep->vaccine_used) return false;
                if (strcasecmp($rep->vaccine_used, $label) === 0) return true;
                foreach ($keywords as $kw) {
                    if (stripos($rep->vaccine_used, $kw) !== false) return true;
                }
                return false;
            })->sum(fn ($r) => ($r->items_received ?? 0) + ($r->opening_stock ?? 0));

            $usedCount = $immunizations->filter(function ($imm) use ($keywords, $label) {
                if (!$imm->vaccine_name) return false;
                if (strcasecmp($imm->vaccine_name, $label) === 0) return true;
                if ($label === 'DT' && stripos($imm->vaccine_name, 'DPT') !== false) {
                    return false;
                }
                foreach ($keywords as $kw) {
                    if (stripos($imm->vaccine_name, $kw) !== false) return true;
                }
                return false;
            })->count();

            $vaccinesLeft = max(0, $storageStock - $usedCount);

            $vaccineRows->push([
                'vaccine_type'       => $label,
                'got_from_storage'   => $storageStock,
                'used_in_clinic'     => $usedCount,
                'vaccines_left'      => $vaccinesLeft,
            ]);
        }

        $allVaccineNames = array_keys(array_merge($this->pediatricVaccines, $this->motherVaccines));

        $prefix = $user->role === 'midwife' ? 'midwife.' : ($user->role === 'mother' ? 'mother.' : 'admin.');

        return view('vaccine-reports.index', [
            'user'             => $user,
            'role'             => $user->role,
            'prefix'           => $prefix,
            'fromDate'         => $fromDate,
            'toDate'           => $toDate,
            'clinicType'       => $clinicType,
            'vaccineRows'      => $vaccineRows,
            'allVaccineNames'  => $allVaccineNames,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        abort_unless(
            $user && in_array($user->role, ['provider', 'admin', 'midwife'], true),
            403
        );

        $validated = $request->validate([
            'clinic_type'    => ['required', 'in:pediatric,mother'],
            'date'           => ['required', 'date'],
            'vaccine_name'   => ['required', 'string', 'max:255'],
            'stock_received' => ['required', 'integer', 'min:1'],
            'batch_no'       => ['nullable', 'string', 'max:100'],
        ]);

        $midwifeId = null;
        if ($user->role === 'midwife' && $user->midwife) {
            $midwifeId = $user->midwife->midwife_id;
        } else {
            $midwifeId = Midwife::query()->value('midwife_id') ?? 1;
        }

        $batchNo = $validated['batch_no'] ?: ('STK-' . date('Ymd') . '-' . strtoupper(Str::random(4)));

        Report::create([
            'midwife_id'              => $midwifeId,
            'clinic_type'             => $validated['clinic_type'],
            'date'                    => $validated['date'],
            'vaccine_used'            => $validated['vaccine_name'],
            'items_received'          => (int) $validated['stock_received'],
            'opening_stock'           => 0,
            'items_returned'          => 0,
            'no_of_vaccine_performed' => 0,
            'closing_stock'           => (int) $validated['stock_received'],
            'batch_no'                => $batchNo,
        ]);

        $prefix = $user->role === 'midwife' ? 'midwife.' : 'admin.';

        return redirect()->route($prefix . 'vaccine-reports.index', [
            'clinic_type' => $validated['clinic_type'],
            'from_date'   => $validated['date'],
            'to_date'     => $validated['date'],
        ])->with('success', 'Storage stock recorded successfully for ' . $validated['vaccine_name'] . ' (' . $validated['stock_received'] . ' doses).');
    }
}