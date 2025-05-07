<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\QuickbooksEstimates;
use App\Models\QuickbooksEstimateItems;
use App\Models\QuickbooksItem;
use App\Models\QuickbooksCustomer;
use App\Models\QuickBooksToken;
use QuickBooksOnline\API\DataService\DataService;
use Illuminate\Support\Facades\Artisan;

class EstimateControllerCopy extends Controller
{
    /**
     * Display the form for creating an estimate.
     */
    public function create()
    {
        $items = QuickbooksItem::all(); // Fetch items from the database
        $customers = QuickbooksCustomer::all(['display_name', 'email']);
        return view('estimate', compact('items', 'customers'));
    }

    /**
     * Handle form submission and store the estimate.
     */
    public function store(Request $request)
    {
        Log::info('Received estimate form submission.', ['request_data' => $request->all()]);

        

        // Run the command to refresh the QuickBooks access token
        try {
            Artisan::call('quickbooks:generate-access-token');
            Log::info('QuickBooks access token refreshed successfully.');
        } catch (\Exception $e) {
            Log::error('Error occurred while refreshing QuickBooks access token.', ['error_message' => $e->getMessage()]);
            return back()->withErrors(['error' => 'An error occurred while refreshing the QuickBooks access token. Please try again.']);
        }

        // Validate form data
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'bill_email' => 'required|email|max:255',
            'product_service' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:1',
            'customer_memo' => 'nullable|string',
            'item_id' => 'required|numeric', // Add validation for item_id
        ]);

        Log::info('Form data validated successfully.', ['validated_data' => $validated]);

        try {
            // Retrieve QuickBooks customer ID based on customer name
            $quickbooksCustomer = QuickbooksCustomer::where('display_name', $validated['customer_name'])->first();
            if (!$quickbooksCustomer) {
                return back()->withErrors(['error' => 'Customer not found in QuickBooks.']);
            }
            $customerId = $quickbooksCustomer->customer_id;

            // Retrieve unit price of the selected product/service
            $item = QuickbooksItem::where('fully_qualified_name', $request->product_service)->first();
            if (!$item) {
                return back()->withErrors(['error' => 'Item not found in QuickBooks.']);
            }
            $unitPrice = $item->unit_price;
            $itemId = $item->item_id;

            // Create a new estimate in your application's database
            $estimate = QuickbooksEstimates::create([
                'customer_name' => $request->customer_name,
                'bill_email' => $request->bill_email,
                'customer_memo' => $request->customer_memo,
            ]);

            // Log data to be submitted to quickbooks_estimate_items
            Log::info('Data to be submitted to quickbooks_estimate_items', [
                'quickbooks_estimate_id' => $estimate->id,
                'sku' => $validated['item_id'], // This should be correct
                'unit_price' => $unitPrice,
                'quantity' => $validated['quantity'],
            ]);

            // Create the estimate item in your application's database
            $estimateItem = QuickbooksEstimateItems::create([
                'quickbooks_estimate_id' => $estimate->id,
                'sku' => $itemId, // Use item_id in the sku column
                'unit_price' => $unitPrice,
                'quantity' => $validated['quantity'],
            ]);

            Log::info('New estimate item created for the estimate.', ['estimate_id' => $estimate->id]);

             // Run the sync-estimates command
             Artisan::call('sync-estimates');

            // Redirect with success message
            return redirect()->route('estimates.create')->with('success', 'Estimate created successfully.');
        } catch (\Exception $e) {
        Log::error('Error occurred while creating estimate.', ['error_message' => $e->getMessage(), 'trace' => $e->getTrace()]);
        return back()->withErrors(['error' => 'An error occurred while creating the estimate. Please try again.']);
        }
    }

    /**
     * Helper method to configure and return DataService instance.
     */
    private function getDataService()
    {
        $token_data = QuickBooksToken::first(); // Retrieve your stored token data
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

    public function showClientEstimateForm()
    {
        // Fetch items from the database
        $items = QuickbooksItem::all();

        // Check if items were retrieved
        if ($items->isEmpty()) {
            // Log a message for debugging purposes
            Log::warning('No items found in QuickbooksItem.');
        }

        // Pass the items to the view
        return view('clientEst', compact('items'));
    }

}
