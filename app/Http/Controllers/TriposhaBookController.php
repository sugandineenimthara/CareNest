<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Child;
use App\Models\Midwife;
use App\Models\Mother;
use App\Models\TriposhaBook;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TriposhaBookController extends Controller
{
    

    public function index(Request $request)
    {
        $user     = Auth::user();
        $search   = $request->query('search');
        $dateFrom = $request->query('date_from');
        $dateTo   = $request->query('date_to');

        
        $query = TriposhaBook::with(['mother', 'child', 'midwife.area']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name_of_mother', 'like', "%{$search}%")
                  ->orWhere('name_of_child', 'like', "%{$search}%")
                  ->orWhere('recipient_type', 'like', "%{$search}%");
            });
        }

        if ($dateFrom) {
            $query->whereDate('issuing_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('issuing_date', '<=', $dateTo);
        }

        
        $allRecords = $query->orderBy('issuing_date', 'desc')
                            ->orderBy('session_label')
                            ->orderBy('serial_no')
                            ->get();

        
        $sessions = $allRecords->groupBy(fn ($r) => $r->issuing_date->format('Y-m-d') . '||' . ($r->session_label ?? 'Session'))
            ->map(function ($records, $key) {
                [$date, $label] = explode('||', $key);
                $totalDistributed = $records->sum('no_of_packets');
                $openingStock     = $records->first()->opening_stock;
                $remaining        = $openingStock !== null ? ($openingStock - $totalDistributed) : null;

                return [
                    'date'             => Carbon::parse($date),
                    'label'            => $label,
                    'records'          => $records,
                    'total_distributed'=> $totalDistributed,
                    'opening_stock'    => $openingStock,
                    'remaining_stock'  => $remaining,
                    'mother_count'     => $records->where('recipient_type', 'Mother')->count(),
                    'child_count'      => $records->where('recipient_type', 'Child')->count(),
                ];
            })
            ->values();

        
        $totalPacketsEver   = TriposhaBook::sum('no_of_packets');
        $totalSessionsEver  = DB::table('triposha_books')
                                ->select('issuing_date', 'session_label')
                                ->groupBy('issuing_date', 'session_label')
                                ->get()
                                ->count();
        $mothersServed      = TriposhaBook::where('recipient_type', 'Mother')->count();
        $childrenServed     = TriposhaBook::where('recipient_type', 'Child')->count();
        $distributedToday   = TriposhaBook::whereDate('issuing_date', today())->sum('no_of_packets');

        $distributionsByDate = TriposhaBook::all()
                                ->groupBy(function ($item) {
                                    return Carbon::parse($item->issuing_date)->format('Y-m-d');
                                })
                                ->map(function ($group) {
                                    return $group->sum('no_of_packets');
                                })
                                ->toArray();

        // Calculate remaining stock from the latest recorded distribution session
        $latestRecordWithStock = TriposhaBook::whereNotNull('opening_stock')
            ->orderBy('issuing_date', 'desc')
            ->orderBy('serial_no', 'desc')
            ->first();

        $defaultOpeningStock = null;
        if ($latestRecordWithStock && $latestRecordWithStock->opening_stock !== null) {
            $sessionPacketsQuery = TriposhaBook::whereDate('issuing_date', $latestRecordWithStock->issuing_date);
            if ($latestRecordWithStock->session_label !== null) {
                $sessionPacketsQuery->where('session_label', $latestRecordWithStock->session_label);
            } else {
                $sessionPacketsQuery->whereNull('session_label');
            }
            $sessionPackets = $sessionPacketsQuery->sum('no_of_packets');
            $defaultOpeningStock = max(0, $latestRecordWithStock->opening_stock - $sessionPackets);
        }

        
        $mothers  = Mother::with('midwife.area')->orderBy('mother_name')->get();
        $children = Child::with('mother', 'midwife.area')->orderBy('child_id')->get();
        $midwives = Midwife::with('area')->get();

        $mothersList = $mothers->map(function ($m) {
            return [
                'id' => $m->mother_id,
                'name' => $m->mother_name,
                'area' => $m->midwife?->area?->area_name ?? 'N/A',
            ];
        })->values();

        $childrenList = $children->map(function ($c) {
            return [
                'id' => $c->child_id,
                'name' => $c->display_name,
                'area' => $c->midwife?->area?->area_name ?? 'N/A',
                'mother' => $c->mother?->mother_name ?? null,
            ];
        })->values();

        return view('triposha.index', compact(
            'user', 'sessions', 'search', 'dateFrom', 'dateTo',
            'totalPacketsEver', 'totalSessionsEver', 'mothersServed', 'childrenServed',
            'distributedToday', 'distributionsByDate', 'defaultOpeningStock',
            'mothers', 'children', 'midwives', 'mothersList', 'childrenList'
        ));
    }

    

    public function storeBatch(Request $request)
    {
        $request->validate([
            'issuing_date'  => 'required|date',
            'session_label' => 'nullable|string|max:100',
            'opening_stock' => 'nullable|integer|min:0',
            'rows'          => 'required|array|min:1',
            'rows.*.recipient_type' => 'required|in:Mother,Child',
            'rows.*.no_of_packets'  => 'required|integer|min:1|max:500',
            'rows.*.mother_id'      => 'nullable|exists:mothers,mother_id',
            'rows.*.child_id'       => 'nullable|exists:children,child_id',
        ]);

        $openingStock = $request->opening_stock;
        if ($openingStock === null) {
            $latestRecordWithStock = TriposhaBook::whereNotNull('opening_stock')
                ->orderBy('issuing_date', 'desc')
                ->orderBy('serial_no', 'desc')
                ->first();
            if ($latestRecordWithStock && $latestRecordWithStock->opening_stock !== null) {
                $sessionPacketsQuery = TriposhaBook::whereDate('issuing_date', $latestRecordWithStock->issuing_date);
                if ($latestRecordWithStock->session_label !== null) {
                    $sessionPacketsQuery->where('session_label', $latestRecordWithStock->session_label);
                } else {
                    $sessionPacketsQuery->whereNull('session_label');
                }
                $sessionPackets = $sessionPacketsQuery->sum('no_of_packets');
                $openingStock = max(0, $latestRecordWithStock->opening_stock - $sessionPackets);
            }
        }

        $midwife      = Midwife::first();
        $user         = Auth::user();
        $midwifeId    = $midwife?->midwife_id;
        $sessionLabel = $request->session_label ?? 'Distribution Session';

        
        if ($user->role === 'midwife') {
            $mw = Midwife::where('user_id', $user->id)->first();
            if ($mw) $midwifeId = $mw->midwife_id;
        }

        DB::beginTransaction();
        try {
            $firstRow = true;
            foreach ($request->rows as $row) {
                $recipientType = $row['recipient_type'];
                $motherId      = null;
                $childId       = null;
                $nameOfMother  = null;
                $nameOfChild   = null;

                if ($recipientType === 'Mother') {
                    $motherId = $row['mother_id'] ?? null;
                    if ($motherId) {
                        $mother      = Mother::find($motherId);
                        $nameOfMother = $mother?->mother_name;
                    }
                } else {
                    $childId = $row['child_id'] ?? null;
                    if ($childId) {
                        $child       = Child::with('mother')->find($childId);
                        $nameOfChild = $child?->display_name;
                        $nameOfMother = $child?->mother?->mother_name;
                        
                        if (!$motherId && $child?->mother_id) {
                            $motherId = $child->mother_id;
                        }
                    }
                }

                TriposhaBook::create([
                    'mother_id'     => $motherId,
                    'child_id'      => $childId,
                    'midwife_id'    => $midwifeId,
                    'no_of_packets' => (int) $row['no_of_packets'],
                    'issuing_date'  => $request->issuing_date,
                    'recipient_type'=> $recipientType,
                    'name_of_mother'=> $nameOfMother,
                    'name_of_child' => $nameOfChild,
                    'opening_stock' => $firstRow ? ($openingStock ?? null) : null,
                    'session_label' => $sessionLabel,
                ]);

                $firstRow = false;
            }

            DB::commit();

            $user = Auth::user();
            $routePrefix = ($user->role === 'midwife') ? 'midwife.' : 'admin.';
            $totalGiven = array_sum(array_column($request->rows, 'no_of_packets'));
            return redirect()->route($routePrefix . 'triposha.index')
                ->with('success', "✅ Thriposha distribution saved! {$totalGiven} packets distributed to " . count($request->rows) . " recipients on " . Carbon::parse($request->issuing_date)->format('d M Y') . ".");
        } catch (\Exception $e) {
            DB::rollBack();
            $user = Auth::user();
            $routePrefix = ($user->role === 'midwife') ? 'midwife.' : 'admin.';
            return redirect()->route($routePrefix . 'triposha.index')->withInput()->with('error', 'Error saving records: ' . $e->getMessage());
        }
    }

    

    public function destroy(int $id)
    {
        TriposhaBook::findOrFail($id)->delete();
        return back()->with('success', 'Record deleted.');
    }
}
