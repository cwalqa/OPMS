<?php

namespace App\Http\Controllers;

use App\Models\DeliveryAgent;
use App\Models\DeliveryLog;
use App\Models\DeliverySchedule;
use App\Models\OrderItemStageLog;
use App\Models\WarehouseLocation;
use App\Models\WarehouseNotification;
use App\Models\QuickbooksEstimateItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DeliveryController extends Controller
{
    /**
     * Display warehouse management dashboard
     */
    public function dashboard()
    {
        $pendingItems = WarehouseNotification::where('status', 'pending')
            ->with(['productionSchedule', 'item.estimate'])
            ->paginate(10, ['*'], 'pending_page');
            
        $assignedItems = WarehouseNotification::where('status', 'assigned')
            ->with(['productionSchedule', 'item.estimate', 'warehouseLocation'])
            ->paginate(10, ['*'], 'assigned_page');
            
        $scheduledItems = WarehouseNotification::where('status', 'scheduled')
            ->with(['productionSchedule', 'item.estimate', 'warehouseLocation', 'deliverySchedule'])
            ->paginate(10, ['*'], 'scheduled_page');
            
        $warehouseLocations = WarehouseLocation::where('is_active', true)
            ->orderBy('zone')
            ->orderBy('aisle')
            ->orderBy('rack')
            ->orderBy('shelf')
            ->get();
            
        return view('admin.warehouse.dashboard', compact(
            'pendingItems', 
            'assignedItems', 
            'scheduledItems', 
            'warehouseLocations'
        ));
    }
    
    /**
     * Assign warehouse location to inventory item
     */
    public function assignLocation(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'warehouse_location_id' => 'required|exists:warehouse_locations,id',
            'storage_notes' => 'nullable|string|max:500',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $warehouseNotification = WarehouseNotification::findOrFail($id);
        
        // Update warehouse notification
        $warehouseNotification->update([
            'warehouse_location_id' => $request->warehouse_location_id,
            'assigned_at' => now(),
            'assigned_by' => Auth::guard('admin')->id(),
            'status' => 'assigned',
            'storage_notes' => $request->storage_notes,
        ]);
        
        // Log the stage change
        OrderItemStageLog::create([
            'tracking_id' => $warehouseNotification->tracking_id,
            'estimate_item_sku' => $warehouseNotification->estimate_item_sku,
            'stage' => 'warehouse_assigned',
            'comments' => 'Product assigned to warehouse location',
            'meta' => [
                'warehouse_location_id' => $request->warehouse_location_id,
                'assigned_by' => Auth::guard('admin')->user()->name ?? 'System',
                'storage_notes' => $request->storage_notes,
            ],
            'timestamp' => now(),
        ]);
        
        return redirect()->route('admin.warehouse.dashboard')
            ->with('success', 'Item successfully assigned to warehouse location');
    }
    
    /**
     * Show form to create delivery schedule
     */
    public function showScheduleDelivery($id)
    {
        $warehouseNotification = WarehouseNotification::with([
            'productionSchedule', 
            'item.estimate', 
            'warehouseLocation'
        ])->findOrFail($id);
        
        $deliveryAgents = DeliveryAgent::with('user')
            ->where('is_active', true)
            ->get();
            
        return view('admin.warehouse.schedule_delivery', compact(
            'warehouseNotification', 
            'deliveryAgents'
        ));
    }
    
    /**
     * Store new delivery schedule
     */
    public function storeDeliverySchedule(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'delivery_date' => 'required|date|after:today',
            'delivery_time_window' => 'required|string|max:50',
            'destination_address' => 'required|string|max:1000',
            'recipient_name' => 'required|string|max:100',
            'recipient_contact' => 'required|string|max:50',
            'delivery_agent_id' => 'required|exists:delivery_agents,id',
            'special_instructions' => 'nullable|string|max:1000',
            'delivery_notes' => 'nullable|string|max:1000',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $warehouseNotification = WarehouseNotification::findOrFail($id);
        
        // Create delivery schedule
        $deliverySchedule = DeliverySchedule::create([
            'warehouse_notification_id' => $warehouseNotification->id,
            'delivery_date' => $request->delivery_date,
            'delivery_time_window' => $request->delivery_time_window,
            'destination_address' => $request->destination_address,
            'recipient_name' => $request->recipient_name,
            'recipient_contact' => $request->recipient_contact,
            'delivery_agent_id' => $request->delivery_agent_id,
            'special_instructions' => $request->special_instructions,
            'delivery_notes' => $request->delivery_notes,
            'status' => 'scheduled',
        ]);
        
        // Update warehouse notification status
        $warehouseNotification->update([
            'status' => 'scheduled',
        ]);
        
        // Log the stage change
        OrderItemStageLog::create([
            'tracking_id' => $warehouseNotification->tracking_id,
            'estimate_item_sku' => $warehouseNotification->estimate_item_sku,
            'stage' => 'delivery_scheduled',
            'comments' => 'Delivery scheduled',
            'meta' => [
                'delivery_date' => $request->delivery_date,
                'delivery_agent_id' => $request->delivery_agent_id,
                'scheduled_by' => Auth::guard('admin')->user()->name ?? 'System',
            ],
            'timestamp' => now(),
        ]);
        
        // Create initial delivery log entry
        DeliveryLog::create([
            'delivery_schedule_id' => $deliverySchedule->id,
            'action' => 'created',
            'notes' => 'Delivery scheduled',
            'user_id' => Auth::guard('admin')->id(),
        ]);
        
        return redirect()->route('admin.warehouse.dashboard')
            ->with('success', 'Delivery successfully scheduled');
    }
    
    /**
     * Manage warehouse locations
     */
    public function manageLocations()
    {
        $locations = WarehouseLocation::orderBy('zone')
            ->orderBy('aisle')
            ->orderBy('rack')
            ->orderBy('shelf')
            ->paginate(20);
            
        return view('admin.warehouse.manage_locations', compact('locations'));
    }
    
    /**
     * Store new warehouse location
     */
    public function storeLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'zone' => 'nullable|string|max:10',
            'aisle' => 'required|string|max:10',
            'rack' => 'required|string|max:10',
            'shelf' => 'required|string|max:10',
            'description' => 'nullable|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Generate location code
        $locationCode = $request->zone 
            ? "{$request->zone}-{$request->aisle}-{$request->rack}-{$request->shelf}"
            : "{$request->aisle}-{$request->rack}-{$request->shelf}";
            
        // Check if location exists
        $exists = WarehouseLocation::where('location_code', $locationCode)->exists();
        if ($exists) {
            return redirect()->back()
                ->with('error', 'This warehouse location already exists')
                ->withInput();
        }
        
        // Create warehouse location
        WarehouseLocation::create([
            'location_code' => $locationCode,
            'zone' => $request->zone,
            'aisle' => $request->aisle,
            'rack' => $request->rack,
            'shelf' => $request->shelf,
            'description' => $request->description,
            'is_active' => true,
        ]);
        
        return redirect()->route('admin.warehouse.locations')
            ->with('success', 'Warehouse location created successfully');
    }
}