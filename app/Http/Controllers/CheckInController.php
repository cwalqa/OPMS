<?php

namespace App\Http\Controllers;

use App\Models\QuickbooksEstimates;
use App\Models\QuickbooksEstimateItems;
use App\Models\Warehouse;
use App\Models\WarehouseItem;
use App\Models\WarehouseLot;
use App\Models\WarehouseShelf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PDF;

class CheckInController extends Controller
{
    public function index(Request $request)
    {
        $query = WarehouseItem::with(['estimate', 'estimateItem']);

        if ($request->filled('printed')) {
            $query->where('is_printed', $request->printed);
        }

        if ($request->filled('packed')) {
            $query->where('is_packed', $request->packed);
        }

        if ($request->filled('check_in_status')) {
            $query->whereHas('estimateItem', function ($q) use ($request) {
                $q->where('check_in_status', $request->check_in_status);
            });
        }

        $items = $query->latest()->paginate(50);
        return view('admin.check_in.index', compact('items'));
    }

    public function show($estimateId)
    {
        $estimate = QuickbooksEstimates::with(['items' => function($query) {
            $query->withCount(['warehouseItems as checked_in_count']);
        }])->findOrFail($estimateId);

        // Get warehouses with their active lots and shelves
        $warehouses = Warehouse::with(['lots' => function($query) {
            $query->where('is_active', true)->orderBy('code');
        }, 'shelves' => function($query) {
            $query->where('is_active', true)->orderBy('code');
        }])->where('is_active', true)->orderBy('name')->get();

        // Create warehouse structure for JavaScript
        $warehouseStructure = $warehouses->map(function ($warehouse) {
            return [
                'id' => $warehouse->id,
                'name' => $warehouse->name,
                'lots' => $warehouse->lots->map(function ($lot) {
                    return [
                        'id' => $lot->id,
                        'code' => $lot->code,
                        'description' => $lot->description ?? '',
                    ];
                })->toArray(),
                'shelves' => $warehouse->shelves->map(function ($shelf) {
                    return [
                        'id' => $shelf->id,
                        'code' => $shelf->code,
                        'description' => $shelf->description ?? '',
                    ];
                })->toArray(),
            ];
        })->toArray();

        logger()->info('🏗️ Warehouse structure for estimate #' . $estimateId, [
            'warehouses_count' => count($warehouses),
            'structure' => $warehouseStructure
        ]);

        return view('admin.check_in.check-in', compact('estimate', 'warehouses', 'warehouseStructure'));
    }

    /**
     * API method to get lots for a specific warehouse
     */
    public function getWarehouseLots($warehouseId)
    {
        logger()->info("🔍 Fetching lots for warehouse #{$warehouseId}");
        
        try {
            $lots = WarehouseLot::where('warehouse_id', $warehouseId)
                               ->where('is_active', true)
                               ->select('id', 'code', 'description')
                               ->orderBy('code')
                               ->get();
            
            logger()->info("✅ Found {$lots->count()} lots for warehouse #{$warehouseId}");
            
            return response()->json($lots);
        } catch (\Exception $e) {
            logger()->error("❌ Error fetching lots for warehouse #{$warehouseId}: " . $e->getMessage());
            return response()->json([], 500);
        }
    }

    /**
     * API method to get shelves for a specific warehouse
     */
    public function getWarehouseShelves($warehouseId)
    {
        logger()->info("🔍 Fetching shelves for warehouse #{$warehouseId}");
        
        try {
            $shelves = WarehouseShelf::where('warehouse_id', $warehouseId) 
                                    ->where('is_active', true)
                                    ->select('id', 'code', 'description')
                                    ->orderBy('code')
                                    ->get();
            
            logger()->info("✅ Found {$shelves->count()} shelves for warehouse #{$warehouseId}");
            
            return response()->json($shelves);
        } catch (\Exception $e) {
            logger()->error("❌ Error fetching shelves for warehouse #{$warehouseId}: " . $e->getMessage());
            return response()->json([], 500);
        }
    }

    public function start()
    {
        $estimates = QuickbooksEstimates::with('items')
            ->where('status', 'approved')
            ->get();

        return view('admin.check_in.start', compact('estimates'));
    }

    public function preview(Request $request, $estimateId)
{
    logger()->info("🔍 PREVIEW BUTTON CLICKED for estimate #{$estimateId}");

    $estimate = QuickbooksEstimates::with('items')->findOrFail($estimateId);
    logger()->info('📦 Loaded estimate', ['estimate' => $estimate->toArray()]);

    logger()->info('📥 Incoming request data', $request->all());

    $validated = $request->validate([
        'items' => 'required|array',
        'items.*.warehouse_id' => 'required|exists:warehouses,id',
        'items.*.lot' => 'nullable|string',
        'items.*.shelf' => 'nullable|string',
    ]);

    logger()->info('✅ Validation passed');

    $previewItems = [];
    $skippedItems = [];

    foreach ($validated['items'] as $itemId => $data) {
        $item = QuickbooksEstimateItems::find($itemId);
        if (!$item) continue;

        $existing = WarehouseItem::where('estimate_item_id', $itemId)->count();
        $remaining = $item->quantity - $existing;

        if ($remaining <= 0) {
            $skippedItems[] = $item;
            continue;
        }

        $qrPreviews = [];
        for ($i = $existing + 1; $i <= $item->quantity; $i++) {
            $seq = str_pad($i, 3, '0', STR_PAD_LEFT);
            $tag = "{$estimate->purchase_order_number}||{$item->sku}||{$item->quantity}||{$seq}";

            $qrData = json_encode([
                'tag' => $tag,
                'purchase_order' => $estimate->purchase_order_number,
                'client_name' => $estimate->customer_name,
                'sku' => $item->sku,
                'product_name' => $item->product_name,
                'sequence' => $seq,
                'warehouse_id' => $data['warehouse_id'],
                'lot' => $data['lot'] ?? null,
                'shelf' => $data['shelf'] ?? null,
            ]);

            $qrSvg = QrCode::size(100)->generate($qrData);
            $qrPreviews[] = ['tag' => $tag, 'qr_svg' => $qrSvg];
        }

        $previewItems[] = [
            'item' => $item,
            'location' => $data,
            'qr_previews' => $qrPreviews,
        ];
    }

    return view('admin.check_in.preview', compact('estimate', 'previewItems', 'skippedItems'));
}




public function process(Request $request, $estimateId)
{
    logger()->info("🔄 PROCESSING check-in for estimate #{$estimateId}");
    logger()->info("📥 Incoming check-in data", $request->all());

    $estimate = QuickbooksEstimates::findOrFail($estimateId);
    $adminId = auth()->guard('admin')->id();

    $items = $request->input('items', []);
    if (empty($items)) {
        return redirect()->route('admin.check_in.show', $estimateId)
            ->withErrors(['error' => 'No items provided.'])
            ->withInput();
    }

    try {
        foreach ($items as $itemId => $data) {
            $item = QuickbooksEstimateItems::findOrFail($itemId);
            $existing = WarehouseItem::where('estimate_item_id', $itemId)->count();
            $quantity = (int) $item->quantity;

            logger()->info("➡ Processing item #$itemId", [
                'warehouse_id' => $data['warehouse_id'] ?? null,
                'lot' => $data['lot'] ?? null,
                'shelf' => $data['shelf'] ?? null,
                'existing_count' => $existing,
                'total_quantity' => $quantity,
            ]);

            for ($i = $existing + 1; $i <= $quantity; $i++) {
                $seq = str_pad($i, 3, '0', STR_PAD_LEFT);
                $tag = "{$estimate->purchase_order_number}||{$item->sku}||{$item->quantity}||{$seq}";

                $qrContent = json_encode([
                    'tag' => $tag,
                    'purchase_order' => $estimate->purchase_order_number,
                    'client_name' => $estimate->customer_name,
                    'sku' => $item->sku,
                    'product_name' => $item->product_name,
                    'sequence' => $seq,
                ]);

                $qrPath = "qrcodes/estimates/{$estimate->id}/{$item->id}_{$seq}.png";
                $qrImage = QrCode::format('png')->size(300)->generate($qrContent);
                Storage::disk('public')->put($qrPath, $qrImage);

                WarehouseItem::create([
                    'estimate_id' => $estimate->id,
                    'estimate_item_id' => $item->id,
                    'warehouse_id' => $data['warehouse_id'],
                    'lot' => $data['lot'] ?? null,
                    'shelf' => $data['shelf'] ?? null,
                    'tag' => $tag,
                    'qr_path' => $qrPath,
                    'sequence_number' => $seq,
                    'checked_in_by' => $adminId,
                ]);

                logger()->info("✅ Created warehouse item", [
                    'item_id' => $item->id,
                    'sequence' => $seq,
                    'tag' => $tag
                ]);
            }

            $item->check_in_status = 'checked_in';
            $item->save();
        }

        $allCheckedIn = $estimate->items()->where('check_in_status', '!=', 'checked_in')->count() === 0;
        if ($allCheckedIn) {
            $estimate->status = 'checked_in';
            $estimate->save();
            logger()->info("✅ Estimate #{$estimateId} fully checked in");
        }

        return redirect()->route('admin.check_in.index')->with('success', 'Items checked in successfully.');
    } catch (\Exception $e) {
        logger()->error("❌ Error during check-in: {$e->getMessage()}");
        return redirect()->route('admin.check_in.show', $estimateId)
            ->withErrors(['error' => 'An error occurred during check-in.'])
            ->withInput();
    }
}







    public function toggleStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'field' => 'required|in:is_printed,is_packed',
            'value' => 'required|boolean',
        ]);

        WarehouseItem::whereIn('id', $request->ids)->update([
            $request->field => $request->value,
        ]);

        return back()->with('success', 'Status updated.');
    }

    public function printLabels($estimateId)
    {
        $estimate = QuickbooksEstimates::with(['items.warehouseItems'])->findOrFail($estimateId);
        return view('admin.check_in.print-labels', compact('estimate'));
    }

    public function generatePdf($estimateId)
    {
        $estimate = QuickbooksEstimates::with('items.warehouseItems')->findOrFail($estimateId);
        $items = $estimate->items->flatMap->warehouseItems;

        $pdf = Pdf::loadView('admin.check_in.labels-pdf', compact('estimate', 'items'));
        return $pdf->download("PO_{$estimate->purchase_order_number}_labels.pdf");
    }

    // MODAL RENDERING METHODS
    public function showModal()
    {
        try {
            $estimates = QuickbooksEstimates::where('status', 'approved')
                ->with(['items' => function($query) {
                    $query->select('id', 'estimate_id', 'check_in_status', 'product_name', 'sku', 'quantity');
                }])
                ->get();
                
            return view('admin.check_in.partials.check-in-modal', compact('estimates'));
        } catch (\Exception $e) {
            logger()->error('Error showing check-in modal: ' . $e->getMessage());
            return response()->view('admin.check_in.partials.error-modal', [
                'message' => 'Failed to load estimates. Please try again.'
            ], 500);
        }
    }

    public function previewModal($estimateId)
    {
        try {
            $estimate = QuickbooksEstimates::with('items')->findOrFail($estimateId);
            $previewItems = [];
            $skippedItems = [];

            foreach ($estimate->items as $item) {
                $existing = WarehouseItem::where('estimate_item_id', $item->id)->count();
                $remaining = $item->quantity - $existing;

                if ($remaining <= 0) {
                    $skippedItems[] = $item;
                    continue;
                }

                $qrPreviews = [];
                for ($i = $existing + 1; $i <= $item->quantity; $i++) {
                    $seq = str_pad($i, 3, '0', STR_PAD_LEFT);
                    $tag = "{$estimate->purchase_order_number}||{$item->sku}||{$item->quantity}||{$seq}";
                    $qrData = json_encode([
                        'tag' => $tag,
                        'purchase_order' => $estimate->purchase_order_number,
                        'client_name' => $estimate->customer_name,
                        'sku' => $item->sku,
                        'product_name' => $item->product_name,
                        'sequence' => $seq,
                    ]);
                    $qrSvg = QrCode::size(100)->generate($qrData);
                    $qrPreviews[] = ['tag' => $tag, 'qr_svg' => $qrSvg];
                }

                $previewItems[] = [
                    'item' => $item,
                    'qr_previews' => $qrPreviews,
                ];
            }

            return view('admin.check_in.partials.preview-modal', compact('estimate', 'previewItems', 'skippedItems'));
        } catch (\Exception $e) {
            logger()->error('Error showing preview modal: ' . $e->getMessage());
            return response()->view('admin.check_in.partials.error-modal', [
                'message' => 'Failed to load preview. Please try again.'
            ], 500);
        }
    }

    public function printLabelsModal($estimateId)
    {
        try {
            $estimate = QuickbooksEstimates::with('items.warehouseItems')
                ->findOrFail($estimateId);
                
            return view('admin.check_in.partials.print-labels-modal', compact('estimate'));
        } catch (\Exception $e) {
            logger()->error('Error showing print labels modal: ' . $e->getMessage());
            return response()->view('admin.check_in.partials.error-modal', [
                'message' => 'Failed to load print labels. Please try again.'
            ], 500);
        }
    }

    /**
     * Helper method to generate QR code data
     * 
     * @param QuickbooksEstimates $estimate
     * @param QuickbooksEstimateItems $item
     * @param string $sequence
     * @return array
     */
    private function generateQrData($estimate, $item, $sequence)
    {
        $tag = "{$estimate->purchase_order_number}||{$item->sku}||{$item->quantity}||{$sequence}";
        
        $qrData = json_encode([
            'tag' => $tag,
            'purchase_order' => $estimate->purchase_order_number,
            'client_name' => $estimate->customer_name,
            'sku' => $item->sku,
            'product_name' => $item->product_name,
            'sequence' => $sequence,
        ]);
        
        $qrSvg = QrCode::size(100)->generate($qrData);
        
        return [
            'tag' => $tag,
            'qr_data' => $qrData,
            'qr_svg' => $qrSvg
        ];
    }
}