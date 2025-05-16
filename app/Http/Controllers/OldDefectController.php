<?php

namespace App\Http\Controllers;

use App\Models\Defect;
use App\Models\QuickbooksEstimateItems;
use Illuminate\Http\Request;

class DefectController extends Controller
{
    // Display all defects
    public function index()
    {
        $defectLogs = Defect::with('item')->paginate(10);
        return view('admin.production.manageDefect', compact('defectLogs'));
    }

    // Show form to report a new defect
    public function create()
    {
        return view('admin.production.reportDefect');
    }

    // Store new defect
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:quickbooks_estimate_items,id',
            'description' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'defect_type' => 'required|string',
            'severity' => 'required|string',
        ]);

        Defect::create($request->all());

        \OrderItemStageLog::create([
            'estimate_item_sku' => $request->item_id,
            'tracking_id' => QuickbooksEstimateItems::find($request->item_id)?->tracking_id,
            'stage' => 'defect_reported',
            'comments' => $request->description,
            'meta' => json_encode([
                'type' => $request->defect_type,
                'severity' => $request->severity ?? null
            ]),
        ]);

        return redirect()->route('defects.index')->with('success', 'Defect reported successfully.');
    }

    // Log notes for a defect
    public function logNotes(Request $request)
    {
        $request->validate([
            'defect_id' => 'required|exists:defects,id',
            'notes' => 'nullable|string',
        ]);

        $defect = Defect::findOrFail($request->defect_id);
        $defect->update(['notes' => $request->notes]);

        return redirect()->route('defects.index')->with('success', 'Notes updated successfully.');
    }

    public function viewManageDefects()
    {
        // Your logic to fetch defect data and other related data goes here.
        // Example data fetching (you may need to adjust this according to your application logic):
        $defects = Defect::all(); // Assuming you have a Defect model.
        
        // Return the view with the data.
        return view('admin.production.manageDefect', compact('defects'));
    }


    // Identify Defect
    public function identifyDefect(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:quickbooks_estimate_items,id',
            'description' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'defect_type' => 'required|string',
        ]);

        Defect::create([
            'item_id' => $request->item_id,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'defect_type' => $request->defect_type,
        ]);

        \OrderItemStageLog::create([
            'estimate_item_sku' => $request->item_id,
            'tracking_id' => QuickbooksEstimateItems::find($request->item_id)?->tracking_id,
            'stage' => 'delivery_scheduled',
            'comments' => $request->delivery_note,
            'meta' => json_encode([
                'dispatch_admin_id' => $request->assigned_dispatch,
                'status' => $request->status,
                'delivery_date' => $request->delivery_date,
            ]),
        ]);
        

        return redirect()->route('admin.manageDefects')->with('success', 'Defect recorded successfully.');
    }

    // Assess Defect Severity
    public function assessSeverity(Request $request, $defect_id)
    {
        $request->validate([
            'severity' => 'required|in:minor,major,critical',
        ]);

        $defect = Defect::findOrFail($defect_id);
        $defect->update(['severity' => $request->severity]);

        return redirect()->route('admin.manageDefects')->with('success', 'Defect severity assessed successfully.');
    }

    // Track Defect Status
    public function trackStatus(Request $request, $defect_id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $defect = Defect::findOrFail($defect_id);
        $defect->update(['status' => $request->status]);

        return redirect()->route('admin.manageDefects')->with('success', 'Defect status updated successfully.');
    }

    // Generate Defect Report
    public function generateDefectReport($item_id)
    {
        $defects = Defect::where('item_id', $item_id)->get();

        // Logic to generate and export defect report
        return view('admin.production.defects.report', compact('defects'));
    }

    // Tag Defective Batches
    public function tagBatch(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|exists:production_batches,id',
            'defect_details' => 'required|string',
        ]);

        $batch = ProductionBatch::findOrFail($request->batch_id);
        $batch->update(['defect_details' => $request->defect_details]);

        return redirect()->route('admin.manageDefects')->with('success', 'Batch tagged with defect details.');
    }

    // Capture Repair History
    public function captureRepairHistory(Request $request, $item_id)
    {
        $request->validate([
            'repair_details' => 'required|string',
            'repair_date' => 'required|date',
        ]);

        $item = QuickbooksEstimateItems::findOrFail($item_id);
        $item->repairHistory()->create([
            'repair_details' => $request->repair_details,
            'repair_date' => $request->repair_date,
        ]);

        return redirect()->route('admin.manageDefects')->with('success', 'Repair history recorded successfully.');
    }

    // Escalate Defect
    public function escalateDefect(Request $request, $defect_id)
    {
        $request->validate([
            'escalation_details' => 'required|string',
        ]);

        $defect = Defect::findOrFail($defect_id);
        // Logic to escalate the defect (e.g., notify management)
        return redirect()->route('admin.manageDefects')->with('success', 'Defect escalated successfully.');
    }

}
