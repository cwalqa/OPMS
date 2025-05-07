<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Warehouse;
use App\Models\WarehouseItem;
use App\Models\Transfer;
use App\Models\ItemHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    /**
     * Display a listing of the transfers.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transfers = Transfer::with(['item', 'sourceWarehouse', 'destinationWarehouse', 'user'])
            ->latest()
            ->paginate(10);
        
        return view('admin.inventory.transfers.index', compact('transfers'));
    }

    /**
     * Show the form for creating a new transfer.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $items = Item::all();
        $warehouses = Warehouse::all();
        
        return view('admin.inventory.transfers.create', compact('items', 'warehouses'));
    }
    
    /**
     * Get warehouse items and locations for an item.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getItemLocations($id)
    {
        $warehouseItems = WarehouseItem::with('warehouse')
            ->where('item_id', $id)
            ->where('stock', '>', 0)
            ->get();
            
        return response()->json($warehouseItems);
    }

    /**
     * Store a newly created transfer in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'source_warehouse_id' => 'required|exists:warehouses,id',
            'destination_warehouse_id' => 'required|exists:warehouses,id|different:source_warehouse_id',
            'source_lot_shelf' => 'nullable|string',
            'destination_lot_shelf' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        // Check if enough stock exists at source
        $sourceItem = WarehouseItem::where('item_id', $request->item_id)
            ->where('warehouse_id', $request->source_warehouse_id)
            ->when($request->source_lot_shelf, function($query) use ($request) {
                return $query->where('lot_shelf', $request->source_lot_shelf);
            })
            ->first();

        if (!$sourceItem || $sourceItem->stock < $request->quantity) {
            return back()->with('error', 'Not enough stock at the source location.');
        }

        DB::beginTransaction();

        try {
            // Reduce stock at source
            $sourceItem->stock -= $request->quantity;
            $sourceItem->save();

            // Add or update stock at destination
            $destinationItem = WarehouseItem::firstOrNew([
                'item_id' => $request->item_id,
                'warehouse_id' => $request->destination_warehouse_id,
                'lot_shelf' => $request->destination_lot_shelf ?? 'A'
            ]);

            if (!$destinationItem->exists) {
                $destinationItem->stock = $request->quantity;
            } else {
                $destinationItem->stock += $request->quantity;
            }

            $destinationItem->save();

            // Create transfer record
            $transfer = new Transfer([
                'item_id' => $request->item_id,
                'source_warehouse_id' => $request->source_warehouse_id,
                'destination_warehouse_id' => $request->destination_warehouse_id,
                'source_lot_shelf' => $request->source_lot_shelf,
                'destination_lot_shelf' => $request->destination_lot_shelf ?? 'A',
                'quantity' => $request->quantity,
                'notes' => $request->notes,
                'user_id' => Auth::id()
            ]);

            $transfer->save();

            // Log in item history
            $item = Item::find($request->item_id);
            
            ItemHistory::create([
                'item_id' => $request->item_id,
                'action' => 'Transfer Out',
                'quantity' => $request->quantity,
                'amount' => $request->quantity * $item->purchase_price,
                'note' => "Transferred from warehouse {$sourceItem->warehouse->name}" . 
                          ($request->source_lot_shelf ? " (Shelf: {$request->source_lot_shelf})" : "")
            ]);

            ItemHistory::create([
                'item_id' => $request->item_id,
                'action' => 'Transfer In',
                'quantity' => $request->quantity,
                'amount' => $request->quantity * $item->purchase_price,
                'note' => "Transferred to warehouse {$destinationItem->warehouse->name}" . 
                          ($request->destination_lot_shelf ? " (Shelf: {$request->destination_lot_shelf})" : "")
            ]);

            DB::commit();

            return redirect()->route('admin.inventory.transfers')->with('success', 'Item transferred successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified transfer.
     *
     * @param  \App\Models\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function show(Transfer $transfer)
    {
        $transfer->load(['item', 'sourceWarehouse', 'destinationWarehouse', 'user']);
        return view('admin.inventory.transfers.show', compact('transfer'));
    }
}