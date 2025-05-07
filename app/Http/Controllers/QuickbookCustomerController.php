<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use QuickBooksOnline\API\DataService\DataService;

use App\Models\QuickBooksCustomer;
use App\Models\QuickBooksToken;
use Illuminate\Support\Facades\Cache;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class QuickbookCustomerController extends Controller
{
    public function getCustomers()
    {
        $token_data = QuickBooksToken::find(1);

        $dataService = DataService::Configure(array(
            'auth_mode'       => 'oauth2',
            'ClientID'        => config('quickbooks.client_id'),
            'ClientSecret'    => config('quickbooks.client_secret'),
            'RedirectURI'     => config('quickbooks.redirect_uri'),
            'baseUrl'         => 'Development',
            'accessTokenKey'  => $token_data->access_token,
            'refreshTokenKey' => $token_data->refresh_token,
            'QBORealmId'      => $token_data->realm_id
        ));

        $date = date('Y-m-d',strtotime('10 day ago'));
        
        $pageSize = 50; // Number of items to retrieve per page
        $startIndex = 1; // Starting index for the first page

        $page = 1;
        $response = [];
        
        do {
            $query = "SELECT * FROM Customer WHERE Metadata.CreateTime>='$date' STARTPOSITION $startIndex MAXRESULTS $pageSize";
            
            $result = $dataService->Query($query);
            
            if(!empty($result))
            {
                $response = array_merge($response, $result);
            }
            else
            {
                $result = array();
            }

            $page++;
            $startIndex = $page * $pageSize + 1;
        } while (count($result) === $pageSize);

        $error = $dataService->getLastError();
       
        if ($error) 
        {
            echo "The Status code is: " . $error->getHttpStatusCode() . "\n";
            echo "The Helper message is: " . $error->getOAuthHelperError() . "\n";
            echo "The Response message is: " . $error->getResponseBody() . "\n";
            exit;
        }
        
        $customer_list = json_decode(json_encode($response), true);
        
        if(!empty($customer_list))
        {
            foreach ($customer_list as $key => $customer) 
            {
                $bill_address = get_value($customer,'BillAddr',array());
                $ship_address = get_value($customer,'ShipAddr',array());
                
                $create_time      = $this->get_conveted_date_time(get_value($customer,'MetaData|CreateTime'));
                $last_updated_time      = $this->get_conveted_date_time(get_value($customer,'MetaData|LastUpdatedTime'));

                $insert_update_master_array = array(
                    'customer_id'  => get_value($customer,'Id'),
                    'display_name' => get_value($customer,'DisplayName'),
                    'phone1'       => get_value($customer,'PrimaryPhone|FreeFormNumber'),
                    'phone2'       => get_value($customer,'AlternatePhone|FreeFormNumber'),
                    'mobile'       => get_value($customer,'Mobile|FreeFormNumber'),
                    // 'fax'           => get_value($customer,'Fax|FreeFormNumber'),

                    'bill_line1'   => get_value($bill_address,'Line1'),
                    'bill_line2'   => get_value($bill_address,'Line2'),
                    'bill_line3'   => get_value($bill_address,'Line3'),
                    'bill_line4'   => get_value($bill_address,'Line4'),
                    'bill_line5'   => get_value($bill_address,'Line5'),
                    'bill_city'    => get_value($bill_address,'City'),
                    'bill_country'   => get_value($bill_address,'Country'),
                    'bill_country_code'   => get_value($bill_address,'CountryCode'),
                    'bill_county'   => get_value($bill_address,'County'),
                    'bill_division_code' => get_value($bill_address,'CountrySubDivisionCode'),
                    'bill_postal_code'   => get_value($bill_address,'PostalCode'),
                    'bill_postal_code_suffix'   => get_value($bill_address,'PostalCodeSuffix'),
                    'bill_email'   => get_value($customer,'PrimaryEmailAddr|Address'),

                    'ship_line1'   => get_value($ship_address,'Line1'),
                    'ship_line2'   => get_value($ship_address,'Line2'),
                    'ship_line3'   => get_value($ship_address,'Line3'),
                    'ship_line4'   => get_value($ship_address,'Line4'),
                    'ship_line5'   => get_value($ship_address,'Line5'),
                    'ship_city'   => get_value($ship_address,'City'),
                    'ship_country'   => get_value($ship_address,'Country'),
                    'ship_country_code'   => get_value($ship_address,'CountryCode'),
                    'ship_county'   => get_value($ship_address,'County'),
                    'ship_division_code' => get_value($ship_address,'CountrySubDivisionCode'),
                    'ship_postal_code'   => get_value($ship_address,'PostalCode'),
                    'ship_postal_code_suffix'   => get_value($ship_address,'PostalCodeSuffix')
                );

                QuickBooksToken::updateOrCreate(
                ['customer_id' => $token_data->realm_id],
                    [
                        'access_token' => $refreshedAccessTokenObj->getAccessToken(),
                        'refresh_token' => $refreshedAccessTokenObj->getRefreshToken(),
                    ]
                );
            }
        }
    }

    // Show the update password form
    public function showUpdatePasswordForm()
    {
        return view('client.update_password'); // Ensure this Blade file exists
    }

    // Handle password update
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $customer = QuickbooksCustomer::find(Auth::id());

        // Check if current password matches
        if (!Hash::check($request->current_password, $customer->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        // Update password & password_changed_at
        $customer->password = Hash::make($request->new_password);
        $customer->password_changed_at = Carbon::now();
        $customer->save();

        return redirect()->route('client.dashboard')->with('success', 'Password updated successfully.');
    }

}
