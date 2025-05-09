<?php

namespace App\Http\Controllers\Api\Quickbooks;

use App\Http\Controllers\Controller;
use App\Models\QuickBooksToken;
use App\Services\QuickBooksTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use QuickBooksOnline\API\DataService\DataService;
use Carbon\Carbon;

class QuickbooksAuthController extends Controller
{
    /**
     * @var QuickBooksTokenService
     */
    protected $tokenService;

    /**
     * Create a new controller instance.
     *
     * @param QuickBooksTokenService $tokenService
     * @return void
     */
    public function __construct(QuickBooksTokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    /**
     * Redirect to QuickBooks authorization page
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authorize()
    {   
        $dataService = $this->tokenService->createAuthService();
        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
        $authorizationCodeUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();
        
        return redirect()->away($authorizationCodeUrl);
    }

    /**
     * Handle the callback from QuickBooks
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback(Request $request)
    {
        try {
            $dataService = DataService::Configure([
                'auth_mode'    => 'oauth2',
                'ClientID'     => config('quickbooks.client_id'),
                'ClientSecret' => config('quickbooks.client_secret'),
                'RedirectURI'  => config('quickbooks.redirect_uri'),
                'scope'        => 'com.intuit.quickbooks.accounting openid profile email phone address',
                'baseUrl'      => config('quickbooks.environment', 'Development'),
            ]);

            $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
            $accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($request->get('code'), $request->get('realmId'));

            // Save tokens to the database with expiration timestamps
            QuickBooksToken::updateOrCreate(
                ['realm_id' => $request->get('realmId')],
                [
                    'access_token' => $accessToken->getAccessToken(),
                    'refresh_token' => $accessToken->getRefreshToken(),
                    'access_token_expires_at' => Carbon::now()->addSeconds($accessToken->getAccessTokenExpiresIn()),
                    'refresh_token_expires_at' => Carbon::now()->addDays(100), // QuickBooks refresh tokens expire after ~100 days
                    'last_used_at' => Carbon::now(),
                    'needs_reauth' => false,
                    'error_message' => null
                ]
            );
            
            // Clear cache
            $this->tokenService->clearTokenCache();

            Log::info('QuickBooks authorization successful. Tokens saved.');
            return redirect('/')->with('success', 'QuickBooks Online authorization successful!');
        } catch (\Exception $e) {
            Log::error('Error during QuickBooks authorization: ' . $e->getMessage());
            return redirect('/')->with('error', 'Error during QuickBooks authorization: ' . $e->getMessage());
        }
    }

    /**
     * Get a valid DataService for making API calls
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function getDataService()
    {
        $result = $this->tokenService->getValidDataService();
        
        if (!$result['success']) {
            if ($result['needs_reauth']) {
                return redirect()->route('quickbooks.authorize')
                    ->with('warning', $result['message']);
            }
            
            return response()->json(['error' => $result['message']], 500);
        }
        
        return $result['data_service'];
    }

    /**
     * Check QuickBooks connection status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkStatus()
    {
        $token = $this->tokenService->getToken();
        
        if (!$token) {
            return response()->json([
                'status' => 'not_connected',
                'message' => 'No QuickBooks connection found.'
            ]);
        }
        
        if ($token->needs_reauth || $token->isRefreshTokenExpired()) {
            return response()->json([
                'status' => 'needs_reauthorization',
                'message' => 'QuickBooks connection requires reauthorization.',
                'auth_url' => route('quickbooks.authorize')
            ]);
        }
        
        if ($token->isAccessTokenExpiringSoon()) {
            // Try to refresh the token
            $refreshResult = $this->tokenService->refreshAccessToken($token);
            
            if (!$refreshResult['success']) {
                return response()->json([
                    'status' => $refreshResult['needs_reauth'] ? 'needs_reauthorization' : 'error',
                    'message' => $refreshResult['message'],
                    'auth_url' => $refreshResult['needs_reauth'] ? route('quickbooks.authorize') : null
                ]);
            }
        }
        
        // Get company info as a test
        try {
            $dataService = $this->tokenService->createDataService($token);
            $companyInfo = $dataService->getCompanyInfo();
            
            return response()->json([
                'status' => 'connected',
                'message' => 'Connected to QuickBooks Online',
                'company' => [
                    'name' => $companyInfo->CompanyName,
                    'address' => $companyInfo->CompanyAddr->Line1 ?? '',
                    'email' => $companyInfo->Email->Address ?? ''
                ],
                'token_expires_at' => $token->access_token_expires_at,
                'refresh_token_expires_at' => $token->refresh_token_expires_at
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking QuickBooks connection: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error connecting to QuickBooks: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Manual token refresh endpoint
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function refreshToken()
    {
        $token = $this->tokenService->getToken();
        
        if (!$token) {
            return redirect()->route('quickbooks.authorize')
                ->with('warning', 'No QuickBooks token found. Please authorize the application.');
        }
        
        if ($token->needs_reauth || $token->isRefreshTokenExpired()) {
            return redirect()->route('quickbooks.authorize')
                ->with('warning', 'QuickBooks refresh token is expired. Please reauthorize the application.');
        }
        
        $result = $this->tokenService->refreshAccessToken($token);
        
        if (!$result['success']) {
            if ($result['needs_reauth']) {
                return redirect()->route('quickbooks.authorize')
                    ->with('warning', $result['message']);
            }
            
            return redirect('/')->with('error', $result['message']);
        }
        
        return redirect('/')->with('success', 'QuickBooks Online authorization credentials refreshed!');
    }

    /**
     * Disconnect QuickBooks
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function disconnect()
    {
        $token = $this->tokenService->getToken();
        
        if ($token) {
            $token->delete();
            $this->tokenService->clearTokenCache();
        }
        
        return redirect('/')->with('success', 'QuickBooks connection removed successfully.');
    }
}