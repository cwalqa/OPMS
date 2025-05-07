<?php

namespace App\Http\Controllers\Api\Quickbooks;

use App\Http\Controllers\Controller;
use App\Models\QuickBooksToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use QuickBooksOnline\API\DataService\DataService;



class QuickbooksAuthController extends Controller
{
    public function authorize()
    {   
        $dataService = DataService::Configure(array(
            'auth_mode'     => 'oauth2',
            'ClientID'      => config('quickbooks.client_id'),
            'ClientSecret'  => config('quickbooks.client_secret'),
            'RedirectURI'   => config('quickbooks.redirect_uri'),
            'scope'         => 'com.intuit.quickbooks.accounting openid profile email phone address',
            'baseUrl'       => config('quickbooks.environment','Development'),
            'response_type' => 'code'
        ));

        // dd(config('quickbooks.client_id'), config('quickbooks.client_secret'), config('quickbooks.redirect_uri'), config('quickbooks.environment'));


        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
        $authorizationCodeUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();
        
        return redirect()->away($authorizationCodeUrl);
    }

    public function callback(Request $request)
    {
        $dataService = DataService::Configure([
            'auth_mode'    => 'oauth2',
            'ClientID'     => config('quickbooks.client_id'),
            'ClientSecret' => config('quickbooks.client_secret'),
            'RedirectURI'  => config('quickbooks.redirect_uri'),
            'scope'        => 'com.intuit.quickbooks.accounting openid profile email phone address',
            'baseUrl'      => config('quickbooks.environment','Development'),
        ]);

        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
        $accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($request->get('code'), $request->get('realmId'));

        // Save tokens to the database
        QuickBooksToken::updateOrCreate(
            ['realm_id' => $request->get('realmId')],
            [
                'access_token' => $accessToken->getAccessToken(),
                'refresh_token' => $accessToken->getRefreshToken(),
            ]
        );
        //dd($accessToken);
        // Store $accessToken for future API requests

        return redirect('/')->with('success', 'QuickBooks Online authorization successful!');
    }

    public function getToken($id)
    {
        // Check cache first
        $tokens = Cache::remember('quick_books_tokens_' . $id, now()->addMinutes(60), function () use ($id) {
            return QuickBooksToken::where('id', $id)->first();
        });

        return $tokens;
    }

    public function getAccessTokenByRefreshToken()
    {
        $token_data =  $this->getToken('1');        
        
        $dataService = DataService::Configure([
            'auth_mode'       => 'oauth2',
            'ClientID'        => config('quickbooks.client_id'),
            'ClientSecret'    => config('quickbooks.client_secret'),
            'accessTokenKey'  => $token_data->access_token,
            'refreshTokenKey' => $token_data->refresh_token,
            'QBORealmId'      => $token_data->realm_id,
            'baseUrl'         => config('quickbooks.environment','Development'),
        ]);

        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();

        $refreshedAccessTokenObj = $OAuth2LoginHelper->refreshToken();

        $error = $OAuth2LoginHelper->getLastError();

        if($error)
        {
            dd($error);
        } 
        else 
        {
            //Refresh Token is called successfully
            $dataService->updateOAuth2Token($refreshedAccessTokenObj);

            // Save tokens to the database
            QuickBooksToken::updateOrCreate(
                ['realm_id' => $token_data->realm_id],
                [
                    'access_token' => $refreshedAccessTokenObj->getAccessToken(),
                    'refresh_token' => $refreshedAccessTokenObj->getRefreshToken(),
                ]
            );

            return redirect('/')->with('success', 'QuickBooks Online authorization credential refreshed!');
        }
    }
}
