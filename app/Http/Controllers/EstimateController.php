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
        Log::info('Received estimate form submission.', ['request_data' => $request->all()]);

        // Refresh QuickBooks access token
        try {
            Artisan::call('quickbooks:generate-access-token');
            Log::info('QuickBooks access token refreshed successfully.');
        } catch (\Exception $e) {
            Log::error('Error occurred while refreshing QuickBooks access token.', ['error_message' => $e->getMessage()]);
            return response()->json(['error' => 'An error occurred while refreshing the QuickBooks access token.'], 500);
        }

        // Validate form data
        $validated = $request->validate([
            'customer_id' => 'required|integer',
            'customer_ref' => 'required|integer',
            'customer_name' => 'required|string|max:255',
            'bill_email' => 'required|email|max:255',
            'customer_memo' => 'nullable|string',
            'quantity' => 'required|array',
            'quantity.*' => 'required|numeric|min:1',
            'item_id' => 'required|array',
            'item_id.*' => 'required|string', 
            'purchase_order_number' => 'required|string',
            'total_amount' => 'required|numeric',
        ]);

        Log::info('Form data validated successfully.', ['validated_data' => $validated]);

        try {
            // Find the QuickBooks customer
            $quickbooksCustomer = QuickbooksCustomer::find($validated['customer_id']);
            if (!$quickbooksCustomer) {
                Log::error('Customer not found in QuickBooks.', ['customer_id' => $validated['customer_id']]);
                return response()->json(['error' => 'Customer not found in QuickBooks.'], 404);
            }

            // Create a new estimate in the application's database
            $estimate = QuickbooksEstimates::create([
                'customer_ref' => $validated['customer_id'],
                'customer_name' => $validated['customer_name'],
                'bill_email' => $validated['bill_email'],
                'customer_memo' => strip_tags($validated['customer_memo']),
                'total_amount' => $validated['total_amount'],
                'purchase_order_number' => $validated['purchase_order_number'],
            ]);

            Log::info('New estimate created in database.', ['estimate_id' => $estimate->id]);

            $orderItems = [];
            
            // Loop through each item and create corresponding estimate items
            foreach ($request->item_id as $index => $itemId) {
                try {
                    // Retrieve unit price of the selected product/service
                    $item = QuickbooksItem::where('item_id', $itemId)->first();
                    if (!$item) {
                        Log::error('Item not found in QuickBooks.', ['item_id' => $itemId]);
                        return response()->json(['error' => 'Item not found in QuickBooks.'], 404);
                    }
                    $unitPrice = $item->unit_price;

                    $trackingId = (string) Str::uuid(); // generate once so it's reused below

                    // Create the estimate item in the application's database
                    $estimateItem = QuickbooksEstimateItems::create([
                        'quickbooks_estimate_id' => $estimate->id,
                        'sku' => $itemId, 
                        'unit_price' => $unitPrice,
                        'quantity' => $request->quantity[$index],
                        'amount' => $unitPrice * $request->quantity[$index],
                        'tracking_id' => $trackingId,
                    ]);

                    $orderItems[] = $estimateItem;

                    Log::info('New estimate item created for the estimate.', [
                        'estimate_id' => $estimate->id,
                        'item_id' => $itemId,
                        'quantity' => $request->quantity[$index],
                    ]);

                    // Generate QR code for the item
                    $qrData = [
                        'client_name' => $validated['customer_name'],
                        'client_ref' => $validated['customer_ref'],
                        'client_id' => $validated['customer_id'],
                        'purchase_order_id' => $estimate->id,
                        'product_name' => $item->name,
                        'product_id' => $itemId,
                        'quantity' => $request->quantity[$index],
                        'additional_notes' => $validated['customer_memo'] ?? 'No notes provided',
                        'tracking_id' => $trackingId,
                    ];



                    // dump('Form Data:', $request->all()); 
                    // dump('QR Data:', $qrData);  
                    // dd('Stopping here to inspect the data');

                    // Encode the data as a string
                    $qrDataString = json_encode($qrData);

                    // Generate the QR code file name in the format PurchaseOrderNumber_ProductName_Quantity.png
                    $qrCodeFileName = $validated['purchase_order_number'] . '_' . str_replace(' ', '_', $item->name) . '_' . $request->quantity[$index] . '.png';
                    $qrCodePath = public_path('qrcodes/' . $qrCodeFileName);

                    // Check if QR directory exists and create if necessary
                    if (!file_exists(public_path('qrcodes'))) {
                        mkdir(public_path('qrcodes'), 0777, true);
                    }

                    // Generate the QR code and save it
                    QrCode::format('png')->size(300)->generate($qrDataString, $qrCodePath);
                    Log::info('QR code generated successfully.', ['qr_code_file' => $qrCodeFileName]);

                    // Save the path to the QR code in the estimate item
                    $estimateItem->qr_code_path = 'qrcodes/' . $qrCodeFileName;
                    $estimateItem->save();
                } catch (\Exception $itemEx) {
                    Log::error('Error processing estimate item.', ['error_message' => $itemEx->getMessage(), 'item_id' => $itemId]);
                    return response()->json(['error' => 'Error occurred while processing item ' . $itemId], 500);
                }
            }

            
            // Run the sync-estimates command
            try {
                Artisan::call('sync-estimates');
                Log::info('Sync estimates command executed successfully.');
            } catch (\Exception $syncEx) {
                Log::error('Error running sync-estimates command.', ['error_message' => $syncEx->getMessage()]);
                return response()->json(['error' => 'Error syncing estimates.'], 500);
            }


            // Proceed to send emails only if sync was successful

            // Send email to client
            if (!empty($estimate->bill_email)) {
                try {
                    SendCustomerOrderMailJob::dispatch($estimate, $orderItems)->delay(now()->addSeconds(5));
                    Log::info('Order acknowledgment email sent to client.', [
                        'estimate_id' => $estimate->id,
                        'email' => $estimate->bill_email,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send order acknowledgment email to client.', [
                        'estimate_id' => $estimate->id,
                        'email' => $estimate->bill_email,
                        'error' => $e->getMessage(),
                    ]);
                }
            } else {
                Log::warning('No customer email found for order acknowledgment notification.', [
                    'estimate_id' => $estimate->id,
                ]);
            }

            // Build recipients list for admin
            $fallbackAdminEmail = config('mail.admin_notification_email', 'jospk.walker@gmail.com');
            $recipients = [$fallbackAdminEmail];

            $adminEmail = auth('admin')->user()->email ?? null;
            if ($adminEmail && $adminEmail !== $fallbackAdminEmail) {
                $recipients[] = $adminEmail;
            }

            // Send email to admin
            try {
                SendAdminOrderMailJob::dispatch($recipients, $estimate, $orderItems)->delay(now()->addSeconds(5));
                Log::info('Purchase order notification email sent to admin.', [
                    'estimate_id' => $estimate->id,
                    'emails' => $recipients,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send purchase order notification to admin.', [
                    'estimate_id' => $estimate->id,
                    'emails' => $recipients,
                    'error' => $e->getMessage(),
                ]);
            }
            return response()->json(['success' => 'Purchase Order submitted successfully!'], 200);

        } catch (\Exception $e) {
            Log::error('Error occurred while creating estimate.', ['error_message' => $e->getMessage(), 'trace' => $e->getTrace()]);
            return response()->json(['error' => 'An error occurred while creating the estimate. Please try again.'], 500);
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
                        ->paginate(10);

        // $purchaseOrders = \App\Models\QuickbooksEstimates::where('customer_ref', Auth::user()->customer_id)
        //     ->where('status', '!=', 'canceled')
        //     ->orderByDesc('created_at')
        //     ->paginate(10);

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

    // Track updated, added, and removed items for reporting
    $updatedItems = [];
    $addedItems = [];
    $removedItems = [];

    // Handle removed items
    if (!empty($request->removed_items)) {
        foreach ($request->removed_items as $removedItemId) {
            $item = QuickbooksEstimateItems::where('id', $removedItemId)
                ->where('quickbooks_estimate_id', $order->id)
                ->first();

            if ($item) {
                $removedItems[] = [
                    'product_id' => $item->sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_cost' => $item->amount,
                ];
                $item->delete();
            }
        }
    }

    // Update existing items
    if (!empty($request->items)) {
        foreach ($request->items as $itemId => $itemData) {
            $item = QuickbooksEstimateItems::where('id', $itemId)
                ->where('quickbooks_estimate_id', $order->id)
                ->first();

            if ($item) {
                $oldQuantity = $item->quantity;
                $item->quantity = $itemData['quantity'];
                $item->amount = $itemData['quantity'] * $itemData['unit_price'];
                $item->save();

                $updatedItems[] = [
                    'product_id' => $item->sku,
                    'old_quantity' => $oldQuantity,
                    'new_quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'total_cost' => $item->amount,
                ];
            }
        }
    }

    // Add new items
    if (!empty($request->new_items)) {
        foreach ($request->new_items as $newItem) {
            if (!empty($newItem['product_id']) && !empty($newItem['quantity']) && !empty($newItem['unit_price'])) {
                $amount = $newItem['quantity'] * $newItem['unit_price'];

                $createdItem = QuickbooksEstimateItems::create([
                    'quickbooks_estimate_id' => $order->id,
                    'sku' => $newItem['product_id'],
                    'quantity' => $newItem['quantity'],
                    'unit_price' => $newItem['unit_price'],
                    'amount' => $amount,
                ]);

                $addedItems[] = [
                    'product_id' => $newItem['product_id'],
                    'quantity' => $newItem['quantity'],
                    'unit_price' => $newItem['unit_price'],
                    'total_cost' => $amount,
                ];
            }
        }
    }

    // Recalculate order total from current active items only
    $currentTotal = QuickbooksEstimateItems::where('quickbooks_estimate_id', $order->id)->sum('amount');

    $order->customer_memo = $request->customer_memo;
    $order->total_amount = $currentTotal;
    $order->save();

    // Prepare email data
    $emailData = [
        'order_number' => $order->purchase_order_number,
        'updated_items' => $updatedItems,
        'added_items' => $addedItems,
        'removed_items' => $removedItems,
        'total_amount' => $currentTotal,
        'customer_memo' => $order->customer_memo,
    ];

    // Send email notifications (queued or immediate)
    Mail::send('emails.order_updated_customer', $emailData, function ($message) use ($order) {
        $message->to($order->bill_email)
            ->subject('Your Order Has Been Updated');
    });

    Mail::send('emails.order_updated_admin', $emailData, function ($message) {
        $message->to('jospk.walker@gmail.com')
            ->subject('An Order Has Been Modified');
    });

    return redirect()->back()->with('success', 'Order updated successfully.');
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
