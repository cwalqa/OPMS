<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\QuickbooksEstimates;
use App\Models\QuickbooksEstimateItems;
use App\Models\QuickbooksItem;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Exception\ServiceException;
use App\Models\QuickBooksToken;
use Illuminate\Http\Request;

class CreateQuickbooksEstimate extends Command
{
    protected $signature = 'quickbooks:create-estimate';

    protected $description = 'Create an estimate in QuickBooks and store locally.';

    public function handle(Request $request)
    {
        // Fetch data dynamically from form submission or other sources
        $customerName = $request->input('customer_name');
        $billEmail = $request->input('bill_email');
        $productService = $request->input('product_service');
        $quantity = $request->input('quantity');
        $customerMemo = $request->input('customer_memo', ''); // Optional, handle gracefully if not provided

        try {
            // Create the estimate locally
            $estimate = QuickbooksEstimates::create([
                'customer_name' => $customerName,
                'bill_email' => $billEmail,
                'customer_memo' => $customerMemo,
            ]);

            // Create the associated estimate item
            $estimateItem = QuickbooksEstimateItems::create([
                'quickbooks_estimate_id' => $estimate->id,
                'description' => $productService,
                'quantity' => $quantity,
                'unit_price' => QuickbooksItem::where('fully_qualified_name', $productService)->value('unit_price'),
            ]);

            // Log local creation success
            Log::info('Local estimate created successfully.', [
                'estimate_id' => $estimate->id,
                'estimate_item_id' => $estimateItem->id,
            ]);

            // Create estimate in QuickBooks
            $this->createQuickbooksEstimate($estimate, $estimateItem);

            $this->info('Estimate created successfully in QuickBooks.');
        } catch (\Exception $e) {
            Log::error('Error occurred while creating estimate.', [
                'error_message' => $e->getMessage(),
            ]);
            $this->error('Error occurred while creating estimate. Check logs for details.');
        }
    }

    protected function createQuickbooksEstimate($estimate, $estimateItem)
    {
        // Fetch OAuth tokens from your database
        $tokenData = QuickBooksToken::find(1);

        // Configure DataService for QuickBooks API
        $dataService = DataService::Configure([
            'auth_mode'       => 'oauth2',
            'ClientID'        => config('quickbooks.client_id'),
            'ClientSecret'    => config('quickbooks.client_secret'),
            'RedirectURI'     => config('quickbooks.redirect_uri'),
            'baseUrl'         => config('quickbooks.environment'),
            'accessTokenKey'  => $tokenData->access_token,
            'refreshTokenKey' => $tokenData->refresh_token,
            'QBORealmId'      => $tokenData->realm_id,
        ]);

        try {
            // Prepare estimate data for QuickBooks
            $estimateData = [
                'Line' => [
                    [
                        'Amount' => $estimateItem->unit_price * $estimateItem->quantity, // Calculate amount based on unit price and quantity
                        'DetailType' => 'SalesItemLineDetail',
                        'SalesItemLineDetail' => [
                            'ItemRef' => [
                                'value' => '1', // Replace with your QuickBooks ItemRef value
                            ],
                            'Qty' => $estimateItem->quantity,
                            'UnitPrice' => $estimateItem->unit_price,
                        ],
                    ],
                ],
                'CustomerRef' => [
                    'value' => '1', // Replace with your QuickBooks CustomerRef value
                ],
            ];

            // Create the estimate in QuickBooks
            $estimateObj = $dataService->Add('estimate', $estimateData);

            // Log success
            Log::info('Estimate created successfully in QuickBooks.', [
                'estimate_id' => $estimateObj->Id,
            ]);
        } catch (ServiceException $exception) {
            Log::error('Error creating estimate in QuickBooks.', [
                'error_message' => $exception->getMessage(),
            ]);
            throw new \Exception('Error creating estimate in QuickBooks: ' . $exception->getMessage());
        }
    }
}
