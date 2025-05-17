<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\QuickbooksEstimates;
use App\Models\QuickBooksToken;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Facades\Estimate as QBOEstimate;
use QuickBooksOnline\API\Facades\Customer;

class SyncEstimates extends Command
{
    protected $signature = 'sync-estimates';
    protected $description = 'Create and update estimates in QuickBooks Online';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $token_data = QuickBooksToken::where('id', "1")->first();

            if($token_data->count()>0)
            {
                // Initialize QuickBooks Data Service
                $this->dataService = DataService::Configure(array(
                    'auth_mode'       => 'oauth2',
                    'ClientID'        => config('quickbooks.client_id'),
                    'ClientSecret'    => config('quickbooks.client_secret'),
                    'RedirectURI'     => config('quickbooks.redirect_uri'),
                    'baseUrl'         => config('quickbooks.environment','Development'),
                    'accessTokenKey'  => $token_data->access_token,
                    'refreshTokenKey' => $token_data->refresh_token,
                    'QBORealmID'      => $token_data->realm_id,
                ));

                $this->dataService->setLogLocation(storage_path('logs/qbo.log'));
                $this->dataService->throwExceptionOnError(true);
               
                // Get all estimates that need to be synced with QuickBooks
                $estimates = QuickbooksEstimates::whereNull('qb_estimate_id')->orWhere('is_updated','1')->with('items')->get();
                
                foreach ($estimates as $estimate) {
                    try {
                        $estimate->customer_ref = $this->findOrCreateCustomer($estimate);
                        
                        if ($estimate->qb_estimate_id) {
                        // Update existing estimate in QuickBooks
                            $qboEstimate = $this->updateEstimateInQBO($estimate);
                            $estimate->is_updated='0';
                        } else {

                        // Create new estimate in QuickBooks
                            $qboEstimate = $this->createEstimateInQBO($estimate); 
                        }

                        if ($qboEstimate) {
                            $estimate->qb_estimate_id = $qboEstimate->Id;
                            $estimate->synced_at = now();
                            $estimate->save();
                        }
                    } catch (\Exception $e) {
                        \Log::error("Error processing estimate ID: " . $estimate->id . " - " . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error("Error in sync-estimates command: " . $e->getMessage());
            throw $e;
        }
    }

    protected function createEstimateInQBO($estimate)
    {
        $estimateData = $this->mapEstimateData($estimate);
        
        $qboEstimate = QBOEstimate::create($estimateData);
        
        $createdEstimate = $this->dataService->Add($qboEstimate);

        if ($createdEstimate) {
            $this->info("Estimate created in QuickBooks: " . $createdEstimate->Id);
            return $createdEstimate;
        } else {
            $error = $this->dataService->getLastError();
            $this->error("Error creating estimate: " . $error->getResponseBody());
            return null;
        }
    }

    protected function updateEstimateInQBO($estimate)
    {
        $estimateData = $this->mapEstimateData($estimate);
        
        $estimate = $this->dataService->FindbyId('estimate', $estimate->qb_estimate_id);

        $qboEstimate = QBOEstimate::update($estimate,$estimateData);

        $updatedEstimate = $this->dataService->Update($qboEstimate);

        if ($updatedEstimate) {
            $this->info("Estimate updated in QuickBooks: " . $updatedEstimate->Id);
            return $updatedEstimate;
        } else {
            $error = $this->dataService->getLastError();
            $this->error("Error updating estimate: " . $error->getResponseBody());
            return null;
        }
    }

    protected function mapEstimateData($estimate)
    {  
        // Map estimate data to QuickBooks format
        $estimateData = [
            'CustomerRef' => [
                'value' => $estimate->customer_ref,
            ],
            "CustomerMemo" => [
                "value" => $estimate->customer_memo
            ],
            "BillEmail" => [
                 "Address" => $estimate->bill_email
            ],
            'Line' => []
        ];

        // Add line items
        foreach ($estimate->items as $item) {
            
            $item_ref = $this->findOrItem($item);
            
            if(empty($item_ref))
            {
                self::error("Items not found in QuickBooks");exit;
            }

            $lineItem = [
                'Amount' => (double)($item->unit_price*$item->quantity),
                'DetailType' => 'SalesItemLineDetail',
                'SalesItemLineDetail' => [
                    'ItemRef' => [
                        'value' => $item_ref,
                    ],
                    'UnitPrice' => $item->unit_price,
                    'Qty' => $item->quantity,
                    'TaxCodeRef' => [
                        'value' => "NON",
                    ]
                ]
            ];

            $estimateData['Line'][] = $lineItem;
        }

        return $estimateData;
    }

    public function findOrCreateCustomer($customerData)
    {
        try {
            // 1. Attempt to Find Existing Customer
            $name = $customerData['customer_name'];
            $existingCustomer = $this->findCustomerByName($name);
        
            if ($existingCustomer) {
                return $existingCustomer->Id; 
            }
        
            // 2. Create New Customer (If Not Found)
            $customer = Customer::create(['DisplayName'=>$name]);
            $resultingObj = $this->dataService->Add($customer);

            if ($resultingObj) {
                return $resultingObj->Id;
            } else {
                $error = $this->dataService->getLastError();
                \Log::error("Failed to create customer in QuickBooks: " . 
                    ($error ? $error->getResponseBody() : "Unknown error"));
                throw new \Exception("Failed to create customer in QuickBooks.");
            }
        } catch (\Exception $e) {
            \Log::error("Error in findOrCreateCustomer: " . $e->getMessage());
            throw $e;
        }
    }

    private function findCustomerByName($name)
    {
        try {
            // Avoid using query syntax since it's causing issues
            // Instead, use FindAll and filter in PHP
            $allCustomers = $this->dataService->FindAll('Customer');
            
            if (!$allCustomers) {
                \Log::error("Failed to retrieve customers from QuickBooks");
                return null;
            }
            
            foreach ($allCustomers as $customer) {
                if (property_exists($customer, 'DisplayName') && $customer->DisplayName == $name) {
                    return $customer;
                }
            }
            
            // No matching customer found
            \Log::info("No customer found with name: " . $name);
            return null;
            
        } catch (\Exception $e) {
            \Log::error("Error in findCustomerByName: " . $e->getMessage());
            return null;
        }
    }

    public function findOrItem($itemData)
    {   
        // 1. Attempt to Find Existing Item
        $existingItem = null;
        
        if (isset($itemData->sku)) {
            $existingItem = $this->findItemBySku($itemData->sku);
        }

        if ($existingItem) {
            return $existingItem->Id;
        }
        else 
        {
            // Log which item is missing
            \Log::warning("Item not found in QuickBooks: " . ($itemData->sku ?? 'Unknown SKU'));
            return false;
        }
    }

    private function findItemBySku($sku)
    {
        try {
            // Completely avoid using Query for now
            // The error is definitely in the Query syntax, so let's bypass it
            
            // Instead, get all items and filter manually
            $allItemsResponse = $this->dataService->FindAll('Item');
            
            if (!$allItemsResponse) {
                \Log::error("Failed to retrieve items from QuickBooks");
                return null;
            }
            
            // Look through all items for a matching SKU or ID
            foreach ($allItemsResponse as $item) {
                // Check if this item matches our SKU (try various property names)
                if (
                    (property_exists($item, 'Id') && $item->Id == $sku) ||
                    (property_exists($item, 'Sku') && $item->Sku == $sku) ||
                    (property_exists($item, 'Name') && $item->Name == $sku) ||
                    (property_exists($item, 'Number') && $item->Number == $sku)
                ) {
                    return $item;
                }
            }
            
            // No matching item found
            \Log::warning("No item found with SKU: " . $sku);
            return null;
            
        } catch (\Exception $e) {
            \Log::error("Error in findItemBySku: " . $e->getMessage());
            return null;
        }
    }
}