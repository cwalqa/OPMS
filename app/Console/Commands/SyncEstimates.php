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
            }
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
            self::error("Failed to create customer in QuickBooks.");
        }
    }

    private function findCustomerByName($name)
    {
        $query = "SELECT Id FROM Customer WHERE DisplayName = '$name'";
        
        $customers = $this->dataService->Query($query);
        
        return !empty($customers) ? $customers[0] : null;
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
            return false;
        }
    }

    private function findItemBySku($sku)
    {
        $query = "SELECT Id FROM Item WHERE Id = '$sku'";
        $items = $this->dataService->Query($query);
        
        return !empty($items) ? $items[0] : null;
    }
}
