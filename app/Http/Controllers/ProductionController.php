<?php

namespace App\Http\Controllers;

use App\Models\ProductionActivityLog;
use App\Models\QuickbooksEstimateItems;
use App\Models\ProductionLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Zxing\QrReader;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Models\ProductionSchedule;
use App\Models\OrderItemStageLog;
use Illuminate\Support\Facades\Response;
use App\Models\ProductionLog;
use App\Models\WarehouseNotification;


class ProductionController extends Controller
{
    // Display all production logs
    public function viewManageProduction()
    {
        $productionLogs = ProductionLog::with(['estimateItem', 'productionLine', 'customer'])->paginate(10);
        $productionLines = ProductionLine::all();

        return view('admin.production.manage', compact('productionLogs', 'productionLines'));
    }

    public function viewStartProduction()
    {
        $scheduledItems = ProductionSchedule::with(['item.order', 'line', 'logs'])
            ->whereIn('schedule_status', ['scheduled', 'in production', 'paused', 'completed'])
            ->paginate(10);
        return view('admin.production.start-production', compact('scheduledItems'));
    }

    // // View Start Production Page
    // // public function viewStartProduction()
    // // {
    // //     $productionLines = ProductionLine::all();
    // //     return view('admin.production.startProduction', compact('productionLines'));
    // // }

    // // Start Production Process (Supports Incremental QR Scans)
    // public function startProduction(Request $request)
    // {
    //     $validator = \Validator::make($request->all(), [
    //         'client_id' => 'required|integer',
    //         'client_ref' => 'required|exists:quickbooks_estimates,customer_ref',
    //         'client_name' => 'required|string',
    //         'purchase_order_id' => 'required|exists:quickbooks_estimates,id',
    //         'product_id' => 'required|exists:quickbooks_estimate_items,sku',
    //         'product_name' => 'required|string',
    //         'quantity' => 'required|integer|min:1',
    //         'production_line_id' => 'required|exists:production_lines,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return redirect()->back()->withErrors($validator)->withInput();
    //     }

    //     $item = QuickbooksEstimateItems::findOrFail($request->product_id);

    //     //Rate Limit: Prevent duplicate triggers within 2 minutes
    //     $recent = OrderItemStageLog::where('estimate_item_sku', $item->sku)
    //         ->where('stage', 'production_start')
    //         ->where('created_at', '>=', now()->subMinutes(2))
    //         ->exists();

    //     if ($recent) {
    //         return redirect()->route('admin.production.manage')->with('error', 'Production already started recently. Please wait before retrying.');
    //     }

    //     if (empty($item->tracking_id)) {
    //         $item->tracking_id = (string) \Str::uuid();
    //         $item->save();
    //     }

    //     OrderItemStageLog::create([
    //         'tracking_id' => $item->tracking_id,
    //         'estimate_item_sku' => $item->sku,  // Use SKU instead of ID
    //         'stage' => 'production_start',
    //         'comments' => 'Production started on line ' . $request->production_line_id,
    //         'meta' => [
    //             'line_id' => $request->production_line_id,
    //             'quantity' => $request->quantity,
    //             'triggered_by' => auth('admin')->user()->name ?? 'System',
    //             'admin_id' => auth('admin')->user()?->id,
    //         ],
    //         'timestamp' => now(),
    //     ]);

    //     $existingLog = ProductionActivityLog::where([
    //         'customer_id' => $request->client_id,
    //         'customer_ref' => $request->client_ref,
    //         'qb_estimate_id' => $request->purchase_order_id,
    //         'estimate_item_id' => $request->product_id,
    //         'production_line_id' => $request->production_line_id,
    //         'status' => 'in-progress',
    //     ])->first();

    //     if ($existingLog) {
    //         $existingLog->increment('order_quantity', $request->quantity);
    //     } else {
    //         ProductionActivityLog::create([
    //             'customer_id' => $request->client_id,
    //             'customer_ref' => $request->client_ref,
    //             'customer_name' => $request->client_name,
    //             'qb_estimate_id' => $request->purchase_order_id,
    //             'estimate_item_id' => $item->sku,
    //             'product_name' => $request->product_name,
    //             'order_quantity' => $request->quantity,
    //             'additional_notes' => $request->additional_notes,
    //             'production_line_id' => $request->production_line_id,
    //             'status' => 'in-progress',
    //             'start_time' => now(),
    //         ]);
    //     }

    //     session()->forget([
    //         'client_id', 'client_ref', 'client_name',
    //         'purchase_order_id', 'product_id', 'product_name',
    //         'quantity', 'additional_notes', 'tracking_logs',
    //     ]);

    //     return redirect()->route('manageProduction')->with('success', 'Production started successfully.');
    // }





    // Upload QR Image & Extract Data
    public function uploadQrImage(Request $request)
    {
        session()->forget([
            'client_id',
            'client_ref',
            'client_name',
            'purchase_order_id',
            'product_id',
            'product_name',
            'quantity',
            'additional_notes',
            'tracking_logs'
        ]);
        
        $request->validate([
            'qr_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $path = $request->file('qr_image')->store('temp');
        $fullPath = storage_path("app/{$path}");

        try {
            $qrcode = new QrReader($fullPath);
            $decodedText = $qrcode->text();

            if (!$decodedText) {
                throw new \Exception('QR code could not be read.');
            }

            $data = json_decode($decodedText, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid QR code format.');
            }

            // Find item and its stage logs
            $item = QuickbooksEstimateItems::where('tracking_id', $data['tracking_id'])->first();
            $logs = $item
                ? OrderItemStageLog::where('estimate_item_sku', $item->sku)
                    ->orderBy('created_at', 'asc')
                    ->get()
                : collect();

            session([
                'client_id' => $data['client_id'] ?? '',
                'client_ref' => $data['client_ref'] ?? '',
                'client_name' => $data['client_name'] ?? '',
                'purchase_order_id' => $data['purchase_order_id'] ?? '',
                'product_id' => $data['product_id'] ?? '',
                'product_name' => $data['product_name'] ?? '',
                'quantity' => $data['quantity'] ?? '',
                'additional_notes' => $data['additional_notes'] ?? '',
                'tracking_logs' => $logs,
            ]);

            return redirect()->route('admin.production.start')->with('show_logs_modal', true);

        } catch (\Exception $e) {
            return redirect()->route('admin.production.start')->with('error', 'Failed to scan QR code: ' . $e->getMessage());
        } finally {
            Storage::delete($path);
        }
    }


    // Update Production (Includes QR Code Update)
    public function updateProduction(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'notes' => 'nullable|string',
            'defects' => 'nullable|string',
            'end_time' => 'nullable|date',
        ]);

        $log = ProductionActivityLog::findOrFail($id);

        $log->update([
            'status' => $request->status,
            'notes' => $request->notes,
            'defects' => $request->defects,
            'end_time' => $request->status == 'completed' ? now() : $log->end_time,
        ]);

        // Generate updated QR Code with new details
        $this->generateQrCode($log);

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
        

        return redirect()->route('production.manage')->with('success', 'Production updated successfully and QR Code regenerated.');
    }

    

    public function viewStageLogs(Request $request)
    {
        $trackingId = $request->input('tracking_id');

        $item = QuickbooksEstimateItems::where('tracking_id', $trackingId)->first();

        if (!$item) {
            return response()->json(['error' => 'Item not found.'], 404);
        }

        $logs = OrderItemStageLog::where('estimate_item_sku', $item->sku)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'item' => $item,
            'logs' => $logs
        ]);
    }

    public function viewStageLogDetails($id)
    {
        // Find the estimate item
        $item = QuickbooksEstimateItems::with('estimate')->find($id);

        if (!$item) {
            return redirect()->back()->withErrors(['error' => 'Item not found.']);
        }

        // Get all stage logs for this item
        $stageLogs = OrderItemStageLog::where('estimate_item_sku', $id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.production.stage_logs', compact('item', 'stageLogs'));
    }


    

    public function startProduction($scheduleId)
    {
        $schedule = ProductionSchedule::with('item')->findOrFail($scheduleId);

        $schedule->update([
            'schedule_status' => 'in production',
            'start_date' => now(),
        ]);

        // Create production log
        ProductionLog::create([
            'production_schedule_id' => $schedule->id,
            'action' => 'start',
            'notes' => 'Production started',
            'user_id' => auth('admin')->user()?->id,
        ]);

        // Update OrderItemStageLog
        OrderItemStageLog::create([
            'tracking_id' => $schedule->item->tracking_id,
            'estimate_item_sku' => $schedule->item->sku,
            'stage' => 'production_start',
            'comments' => 'Production started.',
            'meta' => [
                'started_by' => auth('admin')->user()?->name ?? 'System',
                'production_line' => $schedule->line->line_name,
            ],
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.manageProduction')->with('success', 'Production started for selected item.');
    }

    public function pauseProduction(Request $request, $scheduleId)
    {
        $validated = $request->validate([
            'pause_reason' => 'required|string',
            'defective_quantity' => 'nullable|integer|min:0',
            'defect_notes' => 'nullable|string',
        ]);

        $schedule = ProductionSchedule::with('item')->findOrFail($scheduleId);
        
        $currentDefective = $schedule->defective_quantity ?? 0;
        $newDefective = $validated['defective_quantity'] ?? 0;
        $totalDefective = $currentDefective + $newDefective;
        
        $schedule->update([
            'schedule_status' => 'paused',
            'defective_quantity' => $totalDefective,
            'last_paused_at' => now(),
        ]);

        // Create production log
        ProductionLog::create([
            'production_schedule_id' => $schedule->id,
            'action' => 'pause',
            'notes' => $validated['pause_reason'],
            'user_id' => auth('admin')->user()?->id,
        ]);

        // Update OrderItemStageLog
        OrderItemStageLog::create([
            'tracking_id' => $schedule->item->tracking_id,
            'estimate_item_sku' => $schedule->item->sku,
            'stage' => 'production_paused',
            'comments' => $validated['pause_reason'],
            'meta' => [
                'paused_by' => auth('admin')->user()?->name ?? 'System',
                'defective_quantity' => $newDefective,
                'defect_notes' => $validated['defect_notes'],
            ],
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.manageProduction')->with('success', 'Production paused.');
    }

    public function resumeProduction(Request $request, $scheduleId)
    {
        $validated = $request->validate([
            'resume_notes' => 'nullable|string',
        ]);

        $schedule = ProductionSchedule::with('item')->findOrFail($scheduleId);
        
        $schedule->update([
            'schedule_status' => 'in production',
        ]);

        // Create production log
        ProductionLog::create([
            'production_schedule_id' => $schedule->id,
            'action' => 'resume',
            'notes' => $validated['resume_notes'] ?? 'Production resumed',
            'user_id' => auth('admin')->user()?->id,
        ]);

        // Update OrderItemStageLog
        OrderItemStageLog::create([
            'tracking_id' => $schedule->item->tracking_id,
            'estimate_item_sku' => $schedule->item->sku,
            'stage' => 'production_resumed',
            'comments' => $validated['resume_notes'] ?? 'Production resumed',
            'meta' => [
                'resumed_by' => auth('admin')->user()?->name ?? 'System',
            ],
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.manageProduction')->with('success', 'Production resumed.');
    }

    public function completeProduction(Request $request, $scheduleId)
    {
        $validated = $request->validate([
            'completion_notes' => 'nullable|string',
            'defective_quantity' => 'nullable|integer|min:0',
        ]);

        $schedule = ProductionSchedule::with('item')->findOrFail($scheduleId);
        
        $schedule->update([
            'schedule_status' => 'completed',
            'defective_quantity' => $validated['defective_quantity'] ?? 0,
            'completion_date' => now(),
        ]);

        // Calculate final good quantity
        $goodQuantity = $schedule->quantity - ($validated['defective_quantity'] ?? 0);
        if ($goodQuantity < 0) {
            return redirect()->back()->with('error', 'Defective quantity cannot exceed total quantity.');
        }
        $schedule->update([
            'good_quantity' => $goodQuantity,
        ]);
        // Create production log
        ProductionLog::create([
            'production_schedule_id' => $schedule->id,
            'action' => 'complete',
            'notes' => $validated['completion_notes'] ?? 'Production completed',
            'user_id' => auth('admin')->user()?->id,
        ]);

        // Update OrderItemStageLog
        OrderItemStageLog::create([
            'tracking_id' => $schedule->item->tracking_id,
            'estimate_item_sku' => $schedule->item->sku,
            'stage' => 'production_completed',
            'comments' => $validated['completion_notes'] ?? 'Production completed',
            'meta' => [
                'completed_by' => auth('admin')->user()?->name ?? 'System',
                'good_quantity' => $goodQuantity,
                'defective_quantity' => $validated['defective_quantity'] ?? 0,
            ],
            'timestamp' => now(),
        ]);

        // Notify warehouse
        WarehouseNotification::create([
            'tracking_id' => $schedule->item->tracking_id,
            'estimate_item_sku' => $schedule->item->sku,
            'product_name' => $schedule->item->name ?? 'Unknown Product',
            'quantity' => $goodQuantity,
            'production_schedule_id' => $schedule->id,
            'status' => 'pending',
            'notes' => 'Production completed. Ready for warehouse assignment.',
        ]);

        return redirect()->route('admin.manageProduction')->with('success', 'Production completed successfully. Warehouse has been notified.');
    }
}

