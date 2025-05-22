<?php

namespace App\Http\Controllers;

use App\Models\WarehouseItem;
use Illuminate\Http\Request;

class QrScannerController extends Controller
{
    /**
     * Show QR scanner interface.
     */
    public function index()
    {
        return view('warehouse.qr-scanner');
    }
    
    /**
     * Process scanned QR code and return item details.
     */
    public function scan(Request $request)
    {
        $request->validate([
            'tag' => 'required|string',
        ]);
        
        $tag = $request->input('tag');
        
        // Find the warehouse item by its unique tag
        $warehouseItem = WarehouseItem::with(['order', 'orderItem', 'warehouse'])
            ->where('tag', $tag)
            ->first();
        
        if (!$warehouseItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found. Invalid QR code.',
            ], 404);
        }
        
        // Return item details
        return response()->json([
            'success' => true,
            'item' => [
                'id' => $warehouseItem->id,
                'tag' => $warehouseItem->tag,
                'order_number' => $warehouseItem->order->order_number,
                'client_po' => $warehouseItem->order->client_po_number,
                'system_po' => $warehouseItem->order->system_po_number,
                'item_name' => $warehouseItem->orderItem->name,
                'item_sku' => $warehouseItem->orderItem->sku,
                'sequence' => $warehouseItem->sequence_number,
                'location' => $warehouseItem->location,
                'status' => $warehouseItem->status,
                'qr_url' => $warehouseItem->qrUrl,
            ],
        ]);
    }
    
    /**
     * Update item status based on QR scan
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'tag' => 'required|string',
            'status' => 'required|string|in:in_stock,shipped,consumed,damaged',
        ]);
        
        $tag = $request->input('tag');
        $status = $request->input('status');
        
        $warehouseItem = WarehouseItem::where('tag', $tag)->first();
        
        if (!$warehouseItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found. Invalid QR code.',
            ], 404);
        }
        
        // Update status
        $warehouseItem->status = $status;
        $warehouseItem->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Item status updated successfully.',
            'item' => [
                'id' => $warehouseItem->id,
                'tag' => $warehouseItem->tag,
                'status' => $warehouseItem->status,
            ],
        ]);
    }
}