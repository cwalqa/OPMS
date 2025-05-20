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

    // In SyncEstimates command, modify handle() method:
public function handle()
{
    try {
        $token_data = QuickBooksToken::first(); // Simplified from where('id',1)->first()

        if(!$token_data) {
            \Log::error("QuickBooks token not found");
            return;
        }

        // Initialize DataService
        $this->dataService = DataService::Configure([
            'auth_mode'       => 'oauth2',
            'ClientID'        => config('quickbooks.client_id'),
            'ClientSecret'    => config('quickbooks.client_secret'),
            'RedirectURI'     => config('quickbooks.redirect_uri'),
            'baseUrl'         => config('quickbooks.environment','Development'),
            'accessTokenKey'  => $token_data->access_token,
            'refreshTokenKey' => $token_data->refresh_token,
            'QBORealmID'      => $token_data->realm_id,
        ]);

        $this->dataService->setLogLocation(storage_path('logs/qbo.log'));
        $this->dataService->throwExceptionOnError(true);

        // Get estimates with items
        $estimates = QuickbooksEstimates::where(function($q) {
                $q->whereNull('qb_estimate_id')
                  ->orWhere('is_updated', '1');
            })
            ->with(['items' => function($q) {
                $q->whereNotNull('sku')
                  ->where('quantity', '>', 0);
            }])
            ->has('items', '>', 0) // Only estimates with items
            ->get();

        foreach ($estimates as $estimate) {
            try {
                if ($estimate->items->isEmpty()) {
                    \Log::warning("Estimate #{$estimate->id} has no valid items - skipping");
                    continue;
                }

                $estimate->customer_ref = $this->findOrCreateCustomer($estimate);
                
                if ($estimate->qb_estimate_id) {
                    $qboEstimate = $this->updateEstimateInQBO($estimate);
                    $estimate->is_updated = '0';
                } else {
                    $qboEstimate = $this->createEstimateInQBO($estimate); 
                }

                if ($qboEstimate) {
                    $estimate->qb_estimate_id = $qboEstimate->Id;
                    $estimate->synced_at = now();
                    $estimate->save();
                }
            } catch (\Exception $e) {
                \Log::error("Error processing estimate ID: {$estimate->id} - {$e->getMessage()}");
                \Log::error($e->getTraceAsString());
            }
        }
    } catch (\Exception $e) {
        \Log::error("Error in sync-estimates command: {$e->getMessage()}");
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
    $estimateData = [
        'CustomerRef' => ['value' => $estimate->customer_ref],
        'CustomerMemo' => ['value' => $estimate->customer_memo ?? ''],
        'BillEmail' => ['Address' => $estimate->bill_email],
        'Line' => []
    ];

    if ($estimate->po_date) {
        try {
            $estimateData['TxnDate'] = \Carbon\Carbon::parse($estimate->po_date)->format('Y-m-d');
        } catch (\Exception $e) {
            \Log::error("Invalid po_date for estimate {$estimate->id}: {$estimate->po_date}");
        }
    }

    foreach ($estimate->items as $item) {
        try {
            $item_ref = $this->findOrItem($item);
            
            if(!$item_ref) {
                \Log::error("Item not found in QuickBooks: SKU {$item->sku}");
                continue;
            }

            $estimateData['Line'][] = [
                'Amount' => (float)($item->unit_price * $item->quantity),
                'DetailType' => 'SalesItemLineDetail',
                'Description' => $item->description ?? '',
                'SalesItemLineDetail' => [
                    'ItemRef' => ['value' => $item_ref],
                    'UnitPrice' => (float)$item->unit_price,
                    'Qty' => (float)$item->quantity,
                    'TaxCodeRef' => ['value' => "NON"]
                ]
            ];
        } catch (\Exception $e) {
            \Log::error("Error processing item: {$item->id} - {$e->getMessage()}");
        }
    }

    if(empty($estimateData['Line'])) {
        throw new \Exception("No valid line items for estimate {$estimate->id}");
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