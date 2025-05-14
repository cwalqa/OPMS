<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::paginate(10);
        return view('admin.inventory.warehouses', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:warehouses,name',
            'location' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'lots' => 'nullable|string', // Stored as a comma-separated string
        ]);

        Warehouse::create([
            'name' => $request->name,
            'location' => $request->location,
            'capacity' => $request->capacity,
            'lots' => $request->lots, 
        ]);

        return redirect()->route('inventory.warehouses')->with('success', 'Warehouse added successfully.');
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $request->validate([
            'name' => 'required|string|unique:warehouses,name,' . $warehouse->id,
            'location' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'lots' => 'nullable|string',
        ]);

        $warehouse->update($request->all());

        return redirect()->route('inventory.warehouses')->with('success', 'Warehouse updated successfully.');
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        return redirect()->route('inventory.warehouses')->with('success', 'Warehouse deleted successfully.');
    }

}
