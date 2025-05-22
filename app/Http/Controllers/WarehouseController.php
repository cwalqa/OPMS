<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\WarehouseLot;
use App\Models\WarehouseShelf;
use App\Models\WarehouseItem;
use App\Models\WarehouseLocation;
use App\Models\WarehouseNotification;
use App\Models\OrderItemStageLog;
use App\Models\QuickbooksEstimateItems;
use App\Models\DeliveryAgent;
use App\Models\DeliveryLog;
use App\Models\DeliverySchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{
    /**
     * --- WAREHOUSES ---
     */
    public function index()
    {
        $warehouses = Warehouse::with(['lots', 'shelves'])->orderBy('name')->get();
        return view('admin.warehouse.index', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:warehouses,code',
            'location' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'required|boolean',
        ]);

        Warehouse::create($request->all());
        return back()->with('success', 'Warehouse created successfully.');
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:warehouses,code,' . $warehouse->id,
            'location' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'required|boolean',
        ]);

        $warehouse->update($request->all());
        return back()->with('success', 'Warehouse updated successfully.');
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        return back()->with('success', 'Warehouse deleted successfully.');
    }

    /**
     * --- API METHODS FOR LOTS AND SHELVES ---
     */
    public function getWarehouseLots($warehouseId)
    {
        $lots = WarehouseLot::where('warehouse_id', $warehouseId)
                           ->where('is_active', true)
                           ->select('id', 'code', 'description')
                           ->orderBy('code')
                           ->get();
        
        return response()->json($lots);
    }

    public function getWarehouseShelves($warehouseId)
    {
        $shelves = WarehouseShelf::where('warehouse_id', $warehouseId) 
                                ->where('is_active', true)
                                ->select('id', 'code', 'description')
                                ->orderBy('code')
                                ->get();
        
        return response()->json($shelves);
    }

    /**
     * --- LOTS ---
     */
    public function lots(Warehouse $warehouse)
    {
        $lots = $warehouse->lots()->latest()->get();
        $warehouses = Warehouse::orderBy('name')->get(); // for filter dropdown
        return view('admin.warehouse.lots.index', compact('warehouse', 'lots', 'warehouses'));
    }

    public function storeLot(Request $request, $warehouse)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'code' => 'required|string|max:50|unique:warehouse_lots,code',
            'description' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $warehouseModel = Warehouse::findOrFail($request->warehouse_id);
        $warehouseModel->lots()->create($request->only(['code', 'description', 'is_active']));

        return back()->with('success', 'Lot added successfully.');
    }

    public function updateLot(Request $request, Warehouse $warehouse, WarehouseLot $lot)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:warehouse_lots,code,' . $lot->id,
            'description' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $lot->update($request->all());
        return back()->with('success', 'Lot updated successfully.');
    }

    public function destroyLot(Warehouse $warehouse, WarehouseLot $lot)
    {
        $lot->delete();
        return back()->with('success', 'Lot deleted.');
    }

    /**
     * --- SHELVES ---
     */
    public function warehouseShelves(Warehouse $warehouse)
    {
        $shelves = $warehouse->shelves()->latest()->get();
        $warehouses = Warehouse::orderBy('name')->get(); // For dropdown filter

        return view('admin.warehouse.shelves.index', compact('warehouse', 'shelves', 'warehouses'));
    }

    public function storeShelfFromWarehouse(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:warehouse_shelves,code',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);

        WarehouseShelf::create($validated);

        return redirect()->route('admin.warehouse.shelves.index', $warehouse->id)
                         ->with('success', 'Shelf created successfully.');
    }

    public function updateShelf(Request $request, Warehouse $warehouse, WarehouseShelf $shelf)
    {
        $request->validate([
            'code' => 'required|string|max:255|unique:warehouse_shelves,code,' . $shelf->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'required|boolean',
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);

        $shelf->update($request->only(['warehouse_id', 'code', 'description', 'is_active']));

        return back()->with('success', 'Shelf updated successfully.');
    }

    public function destroyShelf(Warehouse $warehouse, WarehouseShelf $shelf)
    {
        $shelf->delete();
        return back()->with('success', 'Shelf deleted.');
    }
}