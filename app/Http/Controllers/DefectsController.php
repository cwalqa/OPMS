<?php

namespace App\Http\Controllers;

use App\Models\ProductionDefect;
use App\Models\ProductionSchedule;
use App\Models\QuickbooksEstimateItems;
use App\Models\OrderItemStageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DefectsController extends Controller
{
    /**
     * Display a listing of all defects
     */
    public function index(Request $request)
{
    $query = ProductionDefect::with(['productionSchedule', 'estimateItem']);
    $defects = ProductionDefect::with(['estimateItem.order'])->paginate(20);


    // Apply filters if provided
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('severity')) {
        $query->where('severity', $request->severity);
    }

    if ($request->filled('defect_type')) {
        $query->where('defect_type', $request->defect_type);
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('description', 'like', "%$search%")
              ->orWhereHas('estimateItem', function ($q) use ($search) {
                  $q->where('name', 'like', "%$search%")
                    ->orWhere('sku', 'like', "%$search%");
              });
        });
    }

    // Get statuses, severity levels and defect types for filter dropdowns
    $statuses = ProductionDefect::getStatuses();
    $severityLevels = ProductionDefect::getSeverityLevels();
    $defectTypes = ProductionDefect::getDefectTypes();

    // Get defects with pagination
    $defects = $query->orderBy('created_at', 'desc')->paginate(10);

    // 🔥 Check for schedules with defective quantity but no logs
    $schedulesMissingDefectLogs = \App\Models\ProductionSchedule::where('defective_quantity', '>', 0)
        ->whereDoesntHave('defects')
        ->with(['item', 'line']) // Optional: eager load item and line for display
        ->get();

    return view('admin.defects.index', compact(
        'defects',
        'statuses',
        'severityLevels',
        'defectTypes',
        'schedulesMissingDefectLogs' // Pass to view
    ));
}


    /**
     * Show the form for creating a new defect
     */
    public function create(Request $request)
    {
        $productionSchedules = ProductionSchedule::where('defective_quantity', '>', 0)
            ->whereDoesntHave('defects')
            ->with(['item', 'line'])
            ->get();

        $defectTypes = ProductionDefect::getDefectTypes();
        $severityLevels = ProductionDefect::getSeverityLevels();

        // Get selected schedule ID from query string (optional)
        $selectedScheduleId = $request->query('schedule_id');

        return view('admin.defects.create', compact(
            'productionSchedules',
            'defectTypes',
            'severityLevels',
            'selectedScheduleId'
        ));
    }


    /**
     * Store a newly created defect
     */
    public function store(Request $request)
    {
            $validated = $request->validate([
                'production_schedule_id' => 'required|exists:production_schedules,id',
                'defect_type' => 'required|string',
                'severity' => 'required|string',
                'quantity' => 'required|integer|min:1',
                'description' => 'required|string',
                'corrective_action' => 'nullable|string',
            ]);

        // Begin transaction
        DB::beginTransaction();
        
        try {
            // Get production schedule
            $schedule = ProductionSchedule::with('item')->findOrFail($validated['production_schedule_id']);
            
            // Create the defect record
            $defect = ProductionDefect::create([
                'production_schedule_id' => $validated['production_schedule_id'],
                'estimate_item_sku' => $schedule->item->sku,
                'tracking_id' => $schedule->item->tracking_id,
                'defect_type' => $validated['defect_type'],
                'severity' => $validated['severity'],
                'quantity' => $validated['quantity'],
                'description' => $validated['description'],
                'status' => ProductionDefect::STATUS_REPORTED,
                'reported_by' => auth('admin')->user()->id,
                'corrective_action' => $validated['corrective_action'],
            ]);
            
            // If the quantity of defective items is provided, update the production schedule
            if ($validated['quantity'] > 0) {
                $currentDefective = $schedule->defective_quantity ?? 0;
                $newDefective = $validated['quantity'];
                $totalDefective = $currentDefective + $newDefective;
                
                // Update production schedule
                $schedule->update([
                    'defective_quantity' => $totalDefective,
                ]);
            }
            
            // Create a log entry in OrderItemStageLog
            OrderItemStageLog::create([
                'tracking_id' => $schedule->item->tracking_id,
                'estimate_item_sku' => $schedule->item->sku,
                'stage' => 'defect_reported',
                'comments' => $validated['description'],
                'meta' => [
                    'defect_id' => $defect->id,
                    'defect_type' => $validated['defect_type'],
                    'severity' => $validated['severity'],
                    'quantity' => $validated['quantity'],
                    'reported_by' => auth('admin')->user()->name,
                    'corrective_action' => $validated['corrective_action'],
                ],
                'timestamp' => now(),
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.defects.index')->with('success', 'Defect reported successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to report defect: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified defect
     */
    public function show($id)
    {
        $defect = ProductionDefect::with(['productionSchedule', 'estimateItem', 'reporter', 'actionTaker'])->findOrFail($id);
        
        return view('admin.defects.show', compact('defect'));
    }

    /**
     * Show the form for editing the specified defect
     */
    public function edit($id)
    {
        $defect = ProductionDefect::findOrFail($id);
        $defectTypes = ProductionDefect::getDefectTypes();
        $severityLevels = ProductionDefect::getSeverityLevels();
        $statuses = ProductionDefect::getStatuses();
        
        return view('admin.defects.edit', compact('defect', 'defectTypes', 'severityLevels', 'statuses'));
    }

    /**
     * Update the specified defect
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'defect_type' => 'required|string',
            'severity' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'description' => 'required|string',
            'status' => 'required|string',
            'corrective_action' => 'nullable|string',
            'root_cause' => 'nullable|string',
        ]);

        DB::beginTransaction();
        
        try {
            $defect = ProductionDefect::findOrFail($id);
            $oldQuantity = $defect->quantity;
            $schedule = ProductionSchedule::findOrFail($defect->production_schedule_id);
            
            // Update defect record
            $defect->update([
                'defect_type' => $validated['defect_type'],
                'severity' => $validated['severity'],
                'quantity' => $validated['quantity'],
                'description' => $validated['description'],
                'status' => $validated['status'],
                'corrective_action' => $validated['corrective_action'],
                'root_cause' => $validated['root_cause'],
            ]);
            
            // If status is changed to resolved, update action taken details
            if ($validated['status'] === ProductionDefect::STATUS_RESOLVED && $defect->status !== ProductionDefect::STATUS_RESOLVED) {
                $defect->update([
                    'action_taken_by' => auth('admin')->user()->id,
                    'action_taken_at' => now(),
                ]);
            }
            
            // If quantity changed, adjust the production schedule defective quantity
            if ($oldQuantity != $validated['quantity']) {
                $quantityDifference = $validated['quantity'] - $oldQuantity;
                $currentDefective = $schedule->defective_quantity ?? 0;
                $newDefective = $currentDefective + $quantityDifference;
                
                if ($newDefective < 0) {
                    throw new \Exception('Cannot reduce defective quantity below zero.');
                }
                
                $schedule->update([
                    'defective_quantity' => $newDefective,
                ]);
            }
            
            // Create a log entry for the defect update
            OrderItemStageLog::create([
                'tracking_id' => $defect->tracking_id,
                'estimate_item_sku' => $defect->estimate_item_sku,
                'stage' => 'defect_updated',
                'comments' => "Defect ID #{$defect->id} updated. Status: {$validated['status']}",
                'meta' => [
                    'defect_id' => $defect->id,
                    'defect_type' => $validated['defect_type'],
                    'severity' => $validated['severity'],
                    'quantity' => $validated['quantity'],
                    'updated_by' => auth('admin')->user()->name,
                    'status' => $validated['status'],
                    'corrective_action' => $validated['corrective_action'],
                    'root_cause' => $validated['root_cause'],
                ],
                'timestamp' => now(),
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.defects.index')->with('success', 'Defect updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update defect: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Mark a defect for rework
     */
    public function markForRework(Request $request, $id)
    {
        $validated = $request->validate([
            'corrective_action' => 'required|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            $defect = ProductionDefect::findOrFail($id);
            
            $defect->update([
                'status' => ProductionDefect::STATUS_REWORK,
                'corrective_action' => $validated['corrective_action'],
                'action_taken_by' => auth('admin')->user()->id,
                'action_taken_at' => now(),
            ]);
            
            // Log this action
            OrderItemStageLog::create([
                'tracking_id' => $defect->tracking_id,
                'estimate_item_sku' => $defect->estimate_item_sku,
                'stage' => 'defect_rework',
                'comments' => "Defect ID #{$defect->id} marked for rework",
                'meta' => [
                    'defect_id' => $defect->id,
                    'action_by' => auth('admin')->user()->name,
                    'corrective_action' => $validated['corrective_action'],
                ],
                'timestamp' => now(),
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.defects.index')->with('success', 'Defect marked for rework.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to mark defect for rework: ' . $e->getMessage());
        }
    }

    /**
     * Mark a defect for discard
     */
    public function markForDiscard(Request $request, $id)
    {
        $validated = $request->validate([
            'discard_reason' => 'required|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            $defect = ProductionDefect::findOrFail($id);
            
            $defect->update([
                'status' => ProductionDefect::STATUS_DISCARD,
                'corrective_action' => $validated['discard_reason'],
                'action_taken_by' => auth('admin')->user()->id,
                'action_taken_at' => now(),
            ]);
            
            // Log this action
            OrderItemStageLog::create([
                'tracking_id' => $defect->tracking_id,
                'estimate_item_sku' => $defect->estimate_item_sku,
                'stage' => 'defect_discard',
                'comments' => "Defect ID #{$defect->id} marked for discard",
                'meta' => [
                    'defect_id' => $defect->id,
                    'action_by' => auth('admin')->user()->name,
                    'discard_reason' => $validated['discard_reason'],
                ],
                'timestamp' => now(),
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.defects.index')->with('success', 'Defect marked for discard.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to mark defect for discard: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate defect reports
     */
    public function reports(Request $request)
    {
        // Default to current month
        $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->endOfMonth()->format('Y-m-d');
        
        // Defects by type chart data
        $defectsByType = ProductionDefect::whereBetween('created_at', [$startDate, $endDate])
            ->select('defect_type', DB::raw('count(*) as count'))
            ->groupBy('defect_type')
            ->get();
            
        // Defects by severity chart data
        $defectsBySeverity = ProductionDefect::whereBetween('created_at', [$startDate, $endDate])
            ->select('severity', DB::raw('count(*) as count'))
            ->groupBy('severity')
            ->get();
            
        // Top 5 products with most defects
        $topDefectiveProducts = ProductionDefect::whereBetween('created_at', [$startDate, $endDate])
            ->select('estimate_item_sku', DB::raw('sum(quantity) as total_defects'))
            ->with('estimateItem:sku,name')
            ->groupBy('estimate_item_sku')
            ->orderBy('total_defects', 'desc')
            ->limit(5)
            ->get();
            
        // Monthly trend data
        $monthlyTrend = ProductionDefect::whereBetween('created_at', [
                now()->subMonths(6)->startOfMonth(),
                now()->endOfMonth()
            ])
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('count(*) as count')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
            
        return view('admin.defects.reports', compact(
            'defectsByType', 
            'defectsBySeverity', 
            'topDefectiveProducts', 
            'monthlyTrend',
            'startDate',
            'endDate'
        ));
    }
}