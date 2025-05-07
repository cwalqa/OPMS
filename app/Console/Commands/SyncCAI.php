<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\QuickbooksCustomer;
use App\Models\QuickbooksItem;
use App\Models\QuickBooksToken;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Facades\Estimate as QBOEstimate;
use QuickBooksOnline\API\Facades\Item;
use QuickBooksOnline\API\Facades\Customer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class SyncCAI extends Command
{
    protected $signature = 'quickbooks:sync-cai';
    protected $description = 'Sync QBO data';

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
            
            $this->getCustomers();
            $this->getItems();
        }
    }

    public function getCustomers()
    {
        $final_customers_list = array();

        $i = 0;

        while (1)
        {   
            $offset = $i*1000;

            $allCustomer = $this->dataService->FindAll('Customer', $offset, 1000);

            // dd($allCustomer);

            $error = $this->dataService->getLastError();

            if ($error) 
            {
                echo "The Status code is: " . $error->getHttpStatusCode() . "\n";
                echo "The Helper message is: " . $error->getOAuthHelperError() . "\n";
                echo "The Response message is: " . $error->getResponseBody() . "\n";
                exit();
            }
            else
            {
                if (!$allCustomer || (0==count($allCustomer))) 
                {
                    break;
                }
                
                $final_customers_list = array_merge($final_customers_list, $allCustomer);
            }

            $i = $i+1;
        }

        $final_customers_list = json_decode(json_encode($final_customers_list), true);

        $this->createCustomer($final_customers_list);
    }

    public function createCustomer($customer_data)
    { 
        $insert_customer_data = array();

        if(isset($customer_data) && !empty($customer_data))
        {
            foreach($customer_data as $customer_details)
            {
                $is_active = ($customer_details['Active'] == 'true') ? '1'  : '0';
                
                // Generate a default password and hash it
                $defaultPassword = 'defaultPassword'; // You can set this to a more secure default or generate it dynamically
                $hashedPassword = Hash::make($defaultPassword);

                $insert_customer_data[] = array(
                    'customer_id'               => $customer_details['Id'],
                    'fully_qualified_name'      => $customer_details['FullyQualifiedName'], 
                    'company_name'              => $customer_details['CompanyName'],          
                    'display_name'              => $customer_details['DisplayName'],
                    'email'                     => $customer_details['PrimaryEmailAddr']['Address'] ?? null, // Fetch email address
                    'password'                  => $hashedPassword, // Store hashed password
                    'is_active'                 => $is_active
                );
            }
            
            if(!empty($insert_customer_data))
            {
                $resCustomer = QuickbooksCustomer::customerInsertUpdate($insert_customer_data);

                if($resCustomer){
                    self::info('Quickbooks customer data saved successfully');
                    Log::info('Quickbooks customer data saved successfully');
                }
            }
        }
    }

    public function getItems()
    {
        $final_items_list = array();

        $i = 0;

        while (1)
        {   
            $offset = $i*1000;

            $allitem = $this->dataService->FindAll('Item', $offset, 1000);

            $error = $this->dataService->getLastError();

            if ($error) 
            {
                echo "The Status code is: " . $error->getHttpStatusCode() . "\n";
                echo "The Helper message is: " . $error->getOAuthHelperError() . "\n";
                echo "The Response message is: " . $error->getResponseBody() . "\n";
                exit();
            }
            else
            {
                if (!$allitem || (0==count($allitem))) 
                {
                    break;
                }
                
                $final_items_list = array_merge($final_items_list, $allitem);
            }

            $i = $i+1;
        }

        $final_items_list = json_decode(json_encode($final_items_list), true);
        
        $this->createItems($final_items_list);
    }

    public function createItems($item_data)
    { 
        $insert_item_data = array();

        if(isset($item_data) && !empty($item_data))
        {
            foreach($item_data as $item_details)
            {
                $is_active = ($item_details['Active'] == 'true') ? '1'  : '0';

                $insert_item_data[] = array(
                    'item_id'                   => $item_details['Id'],
                    'name'                      => $item_details['Name'],
                    'fully_qualified_name'      => $item_details['FullyQualifiedName'], 
                    'unit_price'                => $item_details['UnitPrice'],          
                    'qty_on_hand'               => $item_details['QtyOnHand'],          
                    'income_account_ref'        => $item_details['IncomeAccountRef'],
                    'item_description'          => $item_details['Description'],
                    'is_active'                 => $is_active
                );
            }
            
            if(!empty($insert_item_data))
            {
                $resitem = QuickbooksItem::itemInsertUpdate($insert_item_data);

                if($resitem){
                    self::info('Quickbooks item data saved successfully');
                    Log::info('Quickbooks item data saved successfully');
                }
            }
        }
    }
}
