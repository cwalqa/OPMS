<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\QuickbooksEstimateItems;
use App\Models\QuickbooksAdmin; // Import ColorwrapAdmin model
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    // Display all deliveries
    public function index()
    {
        $deliveries = Delivery::with('item', 'assignedDispatch')->paginate(10);
        $items = QuickbooksEstimateItems::all();
        $dispatchers = QuickbooksAdmin::all(); // Get dispatchers for modal
        return view('admin.deliveries.manageDeliveries', compact('deliveries', 'items', 'dispatchers'));
    }

    // Show form to create new delivery
    public function create()
    {
        $items = QuickbooksEstimateItems::all();
        $dispatchers = QuickbooksAdmin::all(); // Fetch all available dispatchers
        return view('admin.deliveries.scheduleDelivery', compact('items', 'dispatchers'));
    }

    // Store new delivery
    public function store(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
            'item_id' => 'required|exists:quickbooks_estimate_items,id',
            'quantity' => 'required|integer|min:1',
            'status' => 'required|string',
            'delivery_date' => 'required|date',
            'assigned_dispatch' => 'nullable|exists:quickbooks_admins,id',
            'delivery_note' => 'nullable|string',
        ]);

        Delivery::create($request->all());

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
        

        return redirect()->route('admin.deliveries')->with('success', 'Delivery scheduled successfully.');

    }

    // Show form to edit an existing delivery
    public function edit($id)
    {
        $delivery = Delivery::findOrFail($id);
        $items = QuickbooksEstimateItems::all();
        $dispatchers = QuickbooksAdmin::all(); // Fetch all available dispatchers
        return view('admin.deliveries.editDelivery', compact('delivery', 'items', 'dispatchers'));
    }

    // Update an existing delivery
    public function update(Request $request, $id)
    {
        $request->validate([
            'order_number' => 'required|string',
            'item_id' => 'required|exists:quickbooks_estimate_items,id',
            'quantity' => 'required|integer|min:1',
            'status' => 'required|string',
            'delivery_date' => 'required|date',
            'assigned_dispatch' => 'nullable|exists:quickbooks_admins,id',
            'delivery_note' => 'nullable|string',
        ]);

        $delivery = Delivery::findOrFail($id);
        $delivery->update($request->all());

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
        

        return redirect()->route('admin.deliveries')->with('success', 'Delivery updated successfully.');
    }

    // Log notes for a delivery
    public function logNotes(Request $request)
    {
        $request->validate([
            'delivery_id' => 'required|exists:deliveries,id',
            'notes' => 'nullable|string',
        ]);

        $delivery = Delivery::findOrFail($request->delivery_id);
        $delivery->update(['notes' => $request->notes]);

        return redirect()->route('admin.deliveries')->with('success', 'Notes updated successfully.');
    }
}
