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
     * --- API METHODS ---
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
     * --- LOTS (Global Access) ---
     */
    public function lots(Request $request)
    {
        $selectedWarehouseId = $request->input('warehouse');

        // Retrieve all warehouses for the filter dropdown
        $warehouses = Warehouse::orderBy('name')->get();

        // If a warehouse is selected, filter lots by it
        $lots = WarehouseLot::with('warehouse')
            ->when($selectedWarehouseId, function ($query) use ($selectedWarehouseId) {
                $query->where('warehouse_id', $selectedWarehouseId);
            })
            ->latest()
            ->get();

        return view('admin.warehouse.lots.index', compact('lots', 'warehouses', 'selectedWarehouseId'));
    }


    public function storeLot(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'code' => 'required|string|max:50|unique:warehouse_lots,code',
            'description' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        WarehouseLot::create($request->only(['warehouse_id', 'code', 'description', 'is_active']));
        return back()->with('success', 'Lot added successfully.');
    }

    public function updateLot(Request $request, WarehouseLot $lot)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:warehouse_lots,code,' . $lot->id,
            'description' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $lot->update($request->all());
        return back()->with('success', 'Lot updated successfully.');
    }

    public function destroyLot(WarehouseLot $lot)
    {
        $lot->delete();
        return back()->with('success', 'Lot deleted.');
    }

    /**
     * --- SHELVES (Global Access) ---
     */
    public function warehouseShelves(Request $request)
    {
        $selectedWarehouseId = $request->input('warehouse');

        $warehouses = Warehouse::orderBy('name')->get();

        $shelves = WarehouseShelf::with('warehouse')
            ->when($selectedWarehouseId, function ($query) use ($selectedWarehouseId) {
                $query->where('warehouse_id', $selectedWarehouseId);
            })
            ->latest()
            ->get();

        return view('admin.warehouse.shelves.index', compact('shelves', 'warehouses', 'selectedWarehouseId'));
    }


    public function storeShelfFromWarehouse(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'code' => 'required|string|max:255|unique:warehouse_shelves,code',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'required|boolean',
        ]);

        WarehouseShelf::create($request->only(['warehouse_id', 'code', 'description', 'is_active']));
        return back()->with('success', 'Shelf created successfully.');
    }

    public function updateShelf(Request $request, WarehouseShelf $shelf)
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

    public function destroyShelf(WarehouseShelf $shelf)
    {
        $shelf->delete();
        return back()->with('success', 'Shelf deleted.');
    }
}