<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ItemHistory;
use App\Models\Warehouse;
use App\Models\WarehouseItem;
use Illuminate\Http\Request;

class ItemsController extends Controller
{
    public function index()
    {
        $items = Item::with('category', 'brand')->paginate(10);
        $categories = Category::all();
        $brands = Brand::all(); 
        $warehouses = Warehouse::all(); // Add this line to fetch warehouses

        return view('admin.inventory.items', compact('items', 'categories', 'brands', 'warehouses'));
    }

    public function storeItem(Request $request)
{
    $request->validate([
        'name' => 'required',
        'sku' => 'required|unique:items',
        'brand_id' => 'nullable|exists:brands,id',
        'category_id' => 'nullable|exists:categories,id',
        'default_warehouse_id' => 'nullable|exists:warehouses,id',
        'stock' => 'required|integer|min:1',
        'sale_price' => 'required|numeric|min:0',
        'purchase_price' => 'required|numeric|min:0',
    ]);

    // Create Item (excluding lot_shelf for now)
    $item = Item::create($request->except(['lot_shelf']));

    if ($request->default_warehouse_id) {
        $warehouse = Warehouse::find($request->default_warehouse_id);

        if ($warehouse) {
            $availableLots = explode(',', $warehouse->lots);
            $selectedLot = $request->lot_shelf ?? (count($availableLots) > 0 ? $availableLots[array_rand($availableLots)] : null);

            // Ensure lot is valid
            if (!$selectedLot) {
                return redirect()->back()->withErrors('No valid lot available for the selected warehouse.');
            }

            WarehouseItem::create([
                'warehouse_id' => $warehouse->id,
                'item_id' => $item->id,
                'stock' => $request->stock,
                'lot_shelf' => $selectedLot,
            ]);

            // Log initial stock history
            ItemHistory::create([
                'item_id' => $item->id,
                'action' => 'Initial Stock',
                'quantity' => $request->stock,
                'amount' => $request->stock * $request->purchase_price,
                'note' => "Added to {$warehouse->name}, Lot: {$selectedLot}",
            ]);
        }
    }

    return redirect()->route('inventory.items')->with('success', 'Item added successfully.');
}



    public function updateItem(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required',
            'sku' => 'required|unique:items,sku,' . $item->id,
            'brand_id' => 'nullable|exists:brands,id',
            'category_id' => 'nullable|exists:categories,id',
            'stock' => 'required|integer',
            'sale_price' => 'required|numeric',
            'purchase_price' => 'required|numeric',
        ]);

        // Log stock change
        if ($request->stock > $item->stock) {
            ItemHistory::create([
                'item_id' => $item->id,
                'action' => 'Stock Added',
                'quantity' => $request->stock - $item->stock,
                'amount' => ($request->stock - $item->stock) * $request->purchase_price,
                'note' => 'Additional stock received'
            ]);
        } elseif ($request->stock < $item->stock) {
            ItemHistory::create([
                'item_id' => $item->id,
                'action' => 'Stock Reduced',
                'quantity' => $item->stock - $request->stock,
                'amount' => ($item->stock - $request->stock) * $request->sale_price,
                'note' => 'Stock reduced (sale, damage, return, or adjustment)'
            ]);
        }

        $item->update($request->all());

        return redirect()->route('inventory.items')->with('success', 'Item updated successfully.');
    }



    public function deleteItem(Item $item)
    {
        $item->delete();
        return redirect()->route('inventory.items')->with('success', 'Item deleted successfully.');
    }


    public function show(Item $item)
    {
        $item->load(['histories.warehouse', 'histories', 'warehouseItems.warehouse']);

        $firstEntry = $item->histories()->orderBy('created_at', 'asc')->first();

        return view('admin.inventory.show-item', compact('item', 'firstEntry'));

    }


    public function editItem(Item $item)
    {
        $categories = Category::all();
        $brands = Brand::all();
        return view('admin.inventory.edit-item', compact('item', 'categories', 'brands'));
    }



    
}
