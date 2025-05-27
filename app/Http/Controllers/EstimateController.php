<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\QuickbooksEstimates;
use App\Models\QuickbooksEstimateItems;
use App\Models\QuickbooksItem;
use App\Models\QuickbooksCustomer;
use App\Models\QuickBooksToken;
use Illuminate\Support\Facades\Artisan;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade as PDF;
use App\Services\PHPMailerService;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomerOrderAcknowledgmentMail;
use App\Mail\AdminPurchaseOrderNotificationMail;
use App\Jobs\SendCustomerOrderMailJob;
use App\Jobs\SendAdminOrderMailJob;
use App\Jobs\SendCustomEmailJob;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;






class EstimateController extends Controller
{
    protected $mailer;

    public function __construct(PHPMailerService $mailer)
    {
        $this->mailer = $mailer;
    }

    public function create()
    {
        // Fetch items from the QuickbooksItem model
        $items = QuickbooksItem::all();
        
        // Fetch customer session details
        $customer = QuickbooksCustomer::find(session('customer.id'));

        if ($customer) {
            session([
                'customer_id' => $customer->customer_id, 
                'customer_name' => $customer->company_name, 
            ]);
        }

        // Generate company initials from the customer's company name (or fallback to initials)
        $companyName = $customer->company_name ?? 'Company Name';
        $initials = collect(explode(' ', $companyName))
                        ->map(fn($word) => strtoupper($word[0]))
                        ->implode('');

        // Get the last purchase order number and increment
        $lastOrder = QuickbooksEstimates::latest()->first();
        $orderNumber = $lastOrder ? intval(substr($lastOrder->purchase_order_number, strpos($lastOrder->purchase_order_number, '-') + 1, 4)) + 1 : 1;

        // Format the purchase order number (e.g., DPL-0001-24)
        $poNumber = $initials . '-' . str_pad($orderNumber, 4, '0', STR_PAD_LEFT) . '-' . date('y');

        // Return view with customer_id included
        return view('client.neworder', [
            'items' => $items,
            'customer' => $customer,
            'customerId' => session('customer_id'), // Retrieve from session
            'poNumber' => $poNumber
        ]);
    }


    /**
     * Handle form submission and store the estimate.
     */
public function store(Request $request)
{
    Log::info('Received estimate form submission.', ['request_data' => $request->except('_token')]);

    // 1. Validate input (excluding total_amount)
    try {
        Log::info('Validating estimate form...');
        $validated = $request->validate([
            'customer_id' => 'required|integer|exists:quickbooks_customer,id',
            'customer_ref' => 'required|integer',
            'customer_name' => 'required|string|max:255',
            'bill_email' => 'required|email|max:255',
            'customer_memo' => 'nullable|string',
            'po_date' => 'required|date',
            'purchase_order_number' => 'required|string|unique:quickbooks_estimates,purchase_order_number',
            'client_po_number' => 'required|string|max:255',
            'po_file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:102400',
            'product_service' => 'required|array|min:1',
            'product_service.*' => 'required|string|exists:quickbooks_item,item_id',
            'quantity' => 'required|array',
            'quantity.*' => 'required|numeric|min:0.01',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string|max:500',
        ]);
        Log::info('Validation passed.');
    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation failed.', ['errors' => $e->errors()]);
        return response()->json([
            'error' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    }

    // 2. Refresh QuickBooks token
    try {
        Artisan::call('quickbooks:generate-access-token');
        Log::info('QuickBooks access token refreshed successfully.');
    } catch (\Exception $e) {
        Log::error('QuickBooks token refresh failed', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'Failed to refresh QuickBooks token'], 500);
    }

    // 3. Begin estimate creation
    try {
        DB::beginTransaction();

        // 3.1 Upload PO file
        try {
            $poFilePath = $request->file('po_file')->storeAs(
                'po_documents',
                time() . '_' . Str::slug($validated['client_po_number']) . '.' . $request->file('po_file')->extension(),
                'public'
            );
        } catch (\Exception $e) {
            Log::error('PO file upload failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'File upload failed'], 500);
        }

        // 3.2 Create Estimate
        $estimate = QuickbooksEstimates::create([
            'customer_ref' => $validated['customer_ref'],
            'customer_name' => $validated['customer_name'],
            'bill_email' => $validated['bill_email'],
            'customer_memo' => $validated['customer_memo'] ?? null,
            'purchase_order_number' => $validated['purchase_order_number'],
            'client_po_number' => $validated['client_po_number'],
            'po_document_path' => $poFilePath,
            'po_date' => $validated['po_date'],
            'total_amount' => 0, // Will be updated after item calculation
        ]);

        // 3.3 Process Items
        $calculatedTotal = 0;
        $successfulItems = 0;
        $orderItems = [];

        foreach ($validated['product_service'] as $index => $sku) {
            $item = QuickbooksItem::find($sku);
            if (!$item) {
                Log::warning("Item not found for SKU: $sku");
                continue;
            }

            $quantity = (float)($validated['quantity'][$index] ?? 0);
            if ($quantity <= 0) continue;

            $unitPrice = (float)$item->unit_price;
            $amount = $unitPrice * $quantity;
            $calculatedTotal += $amount;
            $trackingId = Str::uuid()->toString();
            $description = $validated['description'][$index] ?? null;

            $estimateItem = $estimate->items()->create([
                'sku' => $sku,
                'product_name' => $item->name,
                'unit_price' => $unitPrice,
                'quantity' => $quantity,
                'amount' => $amount,
                'description' => $description,
                'tracking_id' => $trackingId,
            ]);

            // QR Code
            $qrData = [
                'estimate_id' => $estimate->id,
                'item_id' => $estimateItem->id,
                'product_id' => $sku,
                'quantity' => $quantity,
                'tracking_id' => $trackingId,
            ];

            $qrPath = 'qrcodes/' . $estimate->purchase_order_number . '_' . $sku . '_' . $trackingId . '.png';
            try {
                QrCode::format('png')->size(300)->generate(json_encode($qrData), public_path($qrPath));
                $estimateItem->update(['qr_code_path' => $qrPath]);
            } catch (\Exception $e) {
                Log::error("QR code generation failed", ['item_id' => $estimateItem->id, 'error' => $e->getMessage()]);
            }

            $orderItems[] = $estimateItem;
            $successfulItems++;
        }

        if ($successfulItems === 0) {
            throw new \Exception('No valid items were processed');
        }

        $estimate->update(['total_amount' => $calculatedTotal]);

        // Optional: warn if mismatch with submitted (if frontend still sends total)
        if ($request->has('total_amount') && abs($calculatedTotal - (float)$request->input('total_amount')) > 0.01) {
            Log::warning('Submitted total does not match calculated total.', [
                'submitted' => $request->input('total_amount'),
                'calculated' => $calculatedTotal
            ]);
        }

        DB::commit();

        // 4. Sync with QuickBooks
        try {
            Artisan::call('sync-estimates');
            Log::info('Estimate sync initiated', ['estimate_id' => $estimate->id]);
        } catch (\Exception $e) {
            Log::error('Estimate sync failed', [
                'estimate_id' => $estimate->id,
                'error' => $e->getMessage()
            ]);
        }

        // 5. Send Emails
        try {
            if (!empty($estimate->bill_email)) {
                SendCustomerOrderMailJob::dispatch($estimate, $orderItems)->delay(now()->addSeconds(5));
            }

            $recipients = array_filter([
                config('mail.admin_notification_email'),
                auth('admin')->user()->email ?? null
            ]);

            if (!empty($recipients)) {
                SendAdminOrderMailJob::dispatch($recipients, $estimate, $orderItems)->delay(now()->addSeconds(10));
            }
        } catch (\Exception $e) {
            Log::error('Email dispatch failed', ['error' => $e->getMessage()]);
        }

        return response()->json([
            'success' => 'Purchase Order submitted successfully!',
            'estimate_id' => $estimate->id
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Estimate creation failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'error' => 'Estimate creation failed: ' . $e->getMessage()
        ], 500);
    }
}





    public function declineOrder(Request $request, $id)
    {
        // Validate the cancel reason
        $request->validate([
            'decline_reason' => 'required|string|max:255',
        ]);

        // Find the order by ID
        $order = QuickbooksEstimates::findOrFail($id);

        // Update the order status and save the cancel reason
        $order->update([
            'status' => 'declined',
            'decline_reason' => $request->decline_reason,
        ]);

        // Generate email content for the customer
        $customerEmailContent = $this->generateDeclineEmailContentForCustomer($order, $request->decline_reason);

        // Generate email content for the admin
        $adminEmailContent = $this->generateDeclineEmailContentForAdmin($order, $request->decline_reason);

        SendCustomEmailJob::dispatch($order->bill_email, 'Your Order Has Been Declined', $customerEmailContent);
        SendCustomEmailJob::dispatch('jospk.walker@gmail.com', 'Order Declined Notification', $adminEmailContent);


        // Redirect back to the admin orders review page with a success message
        return redirect()->route('admin.reviewOrders')->with('success', 'Order declined successfully.');
    }

    /**
     * Log message to browser console.
     *
     * @param string $message
     * @return void
     */
    private function logToBrowserConsole($message)
    {
        echo "<script>console.log(" . json_encode($message) . ");</script>";
    }  

    /**
     * Generate decline email content for the customer.
     *
     * @param QuickbooksEstimates $order
     * @param string $declineReason
     * @return string
     */
    private function generateDeclineEmailContentForCustomer($order, $declineReason)
    {
        return "
        <html>
        <head><title>Order Declined</title></head>
        <body>
            <h1>Dear {$order->customer_name},</h1>
            <p>We regret to inform you that your order placed on {$order->created_at->format('Y-m-d')} with order number {$order->purchase_order_number} has been declined.</p>
            <p><strong>Reason for Decline:</strong> {$declineReason}</p>
            <p>If you have any questions, please contact us.</p>
            <p>Thank you,</p>
            <p>The Admin Team</p>
        </body>
        </html>";
    }

    /**
     * Generate decline email content for the admin.
     *
     * @param QuickbooksEstimates $order
     * @param string $declineReason
     * @return string
     */
    private function generateDeclineEmailContentForAdmin($order, $declineReason)
    {
        return "
        <html>
        <head><title>Order Declined</title></head>
        <body>
            <h1>Order Declined</h1>
            <p><strong>Customer Name:</strong> {$order->customer_name}</p>
            <p><strong>Customer Email:</strong> {$order->bill_email}</p>
            <p><strong>Order Number:</strong> {$order->purchase_order_number}</p>
            <p><strong>Reason for Decline:</strong> {$declineReason}</p>
            <p>Please review the declined order in the admin panel.</p>
            <p>Thank you,</p>
            <p>The Admin Team</p>
        </body>
        </html>";
    }

    /**
     * Helper method to configure and return DataService instance.
     */
    private function getDataService()
    {
        $token_data = QuickBooksToken::first();
        if (!$token_data) {
            throw new \Exception('QuickBooks access token not found.');
        }

        $dataService = DataService::Configure([
            'auth_mode' => 'oauth2',
            'ClientID' => config('quickbooks.client_id'),
            'ClientSecret' => config('quickbooks.client_secret'),
            'accessTokenKey' => $token_data->access_token,
            'refreshTokenKey' => $token_data->refresh_token,
            'QBORealmId' => $token_data->realm_id,
            'baseUrl' => config('quickbooks.environment'),
        ]);
        $dataService->throwExceptionOnError(true);

        return $dataService;
    }

    public function purchaseOrderHistory()
    {
        $customerId = session('customer.customer_id');

        $purchaseOrders = QuickbooksEstimates::where('customer_ref', $customerId)
            ->orderBy('created_at', 'desc')
            ->get(); // Fetch all records

        return view('client.purchase_order_history', compact('purchaseOrders'));
    }


    
    public function viewOrderDetails($orderId)
    {
        // Retrieve the order and its items
        $order = QuickbooksEstimates::with('items')->where('id', $orderId)->first();

        // Check if the order exists
        if (!$order) {
            return redirect()->back()->withErrors(['error' => 'Order not found.']);
        }

        // Retrieve all available items for the dropdown
        $items = QuickbooksItem::all();

        // Pass the order and items data to the view
        return view('client.purchase_order_details', compact('order', 'items'));
    }

    

    public function cancelOrder(Request $request, $id)
    {
        // Validate the cancel reason
        $request->validate([
            'cancel_reason' => 'required|string|max:255',
        ]);
    
        // Find the order by ID
        $order = QuickbooksEstimates::findOrFail($id);
    
        // Update the order status and save the cancel reason
        $order->update([
            'status' => 'canceled',
            'cancel_reason' => $request->cancel_reason,
        ]);
    
        // Generate email content for the customer
        $customerEmailContent = $this->generateCancelEmailContentForCustomer($order, $request->cancel_reason);
    
        // Generate email content for the admin
        $adminEmailContent = $this->generateCancelEmailContentForAdmin($order, $request->cancel_reason);
    
        SendCustomEmailJob::dispatch($order->bill_email, 'Your Order Has Been Canceled', $customerEmailContent);
        SendCustomEmailJob::dispatch('jospk.walker@gmail.com', 'Order Canceled Notification', $adminEmailContent);

    
        // Redirect to the order history with a success message
        return redirect()->route('client.purchaseOrderHistory')->with('success', 'Order canceled successfully.');
    }

    
    private function generateCancelEmailContentForCustomer($order, $cancelReason)
    {
        return "
        <html>
        <head><title>Order Canceled</title></head>
        <body>
            <h1>Dear {$order->customer_name},</h1>
            <p>We have received your initiated request to cancel your order placed on {$order->created_at->format('Y-m-d')} with order number {$order->purchase_order_number}.</p>
            <p><strong>Your stated reason for Cancellation:</strong> {$cancelReason}</p>
            <p>If you have any questions or you did not initiate this cancellation, please contact us.</p>
            <p>Thank you,</p>
            <p>The Support Team</p>
        </body>
        </html>";
    }


    private function generateCancelEmailContentForAdmin($order, $cancelReason)
    {
        return "
        <html>
        <head><title>Order Canceled</title></head>
        <body>
            <h1>Order Canceled</h1>
            <p><strong>Customer Name:</strong> {$order->customer_name}</p>
            <p><strong>Customer Email:</strong> {$order->bill_email}</p>
            <p><strong>Order Number:</strong> {$order->purchase_order_number}</p>
            <p><strong>Reason for Cancellation:</strong> {$cancelReason}</p>
            <p>Please review the canceled order in the admin panel.</p>
            <p>Thank you,</p>
            <p>The Admin Team</p>
        </body>
        </html>";
    }



    public function updateOrder(Request $request, $id)
{
    $order = QuickbooksEstimates::with('items')->findOrFail($id);

    $updatedItems = [];
    $addedItems = [];
    $removedItems = [];

    // Remove items
    if ($request->removed_items) {
        foreach ($request->removed_items as $removedId) {
            $item = QuickbooksEstimateItems::where('id', $removedId)
                ->where('quickbooks_estimate_id', $order->id)
                ->first();

            if ($item) {
                $product = \App\Models\QuickbooksItem::where('item_id', $item->sku)->first();

                $removedItems[] = [
                    'name' => $product?->name ?? 'Unknown',
                    'product_id' => $item->sku,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                ];

                $item->delete();
            }
        }
    }

    // Update existing items
    foreach ($request->items ?? [] as $itemId => $data) {
        $item = QuickbooksEstimateItems::where('id', $itemId)
            ->where('quickbooks_estimate_id', $order->id)
            ->first();

        if ($item) {
            $originalQty = $item->quantity;
            $item->quantity = $data['quantity'] ?? 1;
            $item->unit_price = $data['unit_price'] ?? 0;
            $item->description = $data['description'] ?? '';
            $item->amount = $item->quantity * $item->unit_price;

            $item->save();

            $product = \App\Models\QuickbooksItem::where('item_id', $item->sku)->first();

            $updatedItems[] = [
                'name' => $product?->name ?? 'Unknown',
                'product_id' => $item->sku,
                'description' => $item->description,
                'old_quantity' => $originalQty,
                'new_quantity' => $item->quantity,
                'unit_price' => number_format($item->unit_price, 2),
                'total_cost' => number_format($item->quantity * $item->unit_price, 2),
            ];
        }
    }

    // Add new items
    foreach ($request->new_items ?? [] as $new) {
        if (!empty($new['product_id']) && !empty($new['quantity'])) {
            $unitPrice = $new['unit_price'] ?? 0;
            $quantity = $new['quantity'];

           $item = QuickbooksEstimateItems::create([
                'quickbooks_estimate_id' => $order->id,
                'sku' => $new['product_id'],
                'description' => $new['description'] ?? '',
                'quantity' => $new['quantity'],
                'unit_price' => $new['unit_price'],
                'amount' => $new['quantity'] * $new['unit_price'],
            ]);


            $product = \App\Models\QuickbooksItem::where('item_id', $item->sku)->first();

            $addedItems[] = [
                'name' => $product?->name ?? 'Unknown',
                'product_id' => $item->sku,
                'description' => $item->description,
                'quantity' => $item->quantity,
            ];
        }
    }

    // Update order summary
    $order->total_amount = $order->items()->sum('amount');
    $order->customer_memo = $request->customer_memo;
    $order->save();

    // Send update emails
    Mail::send('emails.order_updated_customer', compact('order', 'updatedItems', 'addedItems', 'removedItems'), function ($msg) use ($order) {
        $msg->to($order->bill_email)->subject('Order Updated');
    });

    Mail::send('emails.order_updated_admin', compact('order', 'updatedItems', 'addedItems', 'removedItems'), function ($msg) {
        $msg->to('jospk.walker@gmail.com')->subject('Client Order Updated');
    });

    return redirect()->back()->with('success', 'Order updated.');
}


    

    public function canceledOrderHistory()
    {
        $customerId = session('customer.customer_id');

        // Fetch only canceled orders for the logged-in customer
        $canceledOrders = QuickbooksEstimates::where('customer_ref', $customerId)
                            ->where('status', 'canceled') // Filter only canceled orders
                            ->orderBy('created_at', 'desc')
                            ->get();

        return view('client.canceled_order_history', compact('canceledOrders'));
    }


    public function declinedOrderHistory()
    {
        $customerId = session('customer.customer_id');

        // Fetch only canceled orders for the logged-in customer
        $declinedOrders = QuickbooksEstimates::where('customer_ref', $customerId)
                            ->where('status', 'declined') // Filter only canceled orders
                            ->orderBy('created_at', 'desc')
                            ->get();

        return view('client.declined_order_history', compact('declinedOrders'));
    }

    public function viewCanceledOrderDetails($orderId)
    {
        // Retrieve the order and its items
        $order = QuickbooksEstimates::with('items')->where('id', $orderId)->first();

        // Check if the order exists
        if (!$order) {
            return redirect()->back()->withErrors(['error' => 'Order not found.']);
        }

        // Retrieve all available items for the dropdown
        $items = QuickbooksItem::all();

        // Pass the order and items data to the view
        return view('client.canceled_order_details', compact('order', 'items'));
    }




}
