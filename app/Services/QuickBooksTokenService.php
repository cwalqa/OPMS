<?php

namespace App\Services;

use App\Models\QuickBooksToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use QuickBooksOnline\API\DataService\DataService;

class QuickBooksTokenService
{
    /**
     * Get the QuickBooks token by ID
     * 
     * @param int $id
     * @return QuickBooksToken|null
     */
    public function getToken($id = 1)
    {
        // Check cache first for performance
        return Cache::remember('quickbooks_token_' . $id, now()->addMinutes(5), function () use ($id) {
            return QuickBooksToken::find($id);
        });
    }

    /**
     * Clear the token cache
     * 
     * @param int $id
     * @return void
     */
    public function clearTokenCache($id = 1)
    {
        Cache::forget('quickbooks_token_' . $id);
    }

    /**
     * Create a configured DataService instance
     * 
     * @param QuickBooksToken $token
     * @return DataService
     */
    public function createDataService(QuickBooksToken $token)
    {
        return DataService::Configure([
            'auth_mode'       => 'oauth2',
            'ClientID'        => config('quickbooks.client_id'),
            'ClientSecret'    => config('quickbooks.client_secret'),
            'accessTokenKey'  => $token->access_token,
            'refreshTokenKey' => $token->refresh_token,
            'QBORealmId'      => $token->realm_id,
            'baseUrl'         => config('quickbooks.environment', 'Development'),
        ]);
    }

    /**
     * Create DataService for authorization
     * 
     * @return DataService
     */
    public function createAuthService()
    {
        return DataService::Configure([
            'auth_mode'     => 'oauth2',
            'ClientID'      => config('quickbooks.client_id'),
            'ClientSecret'  => config('quickbooks.client_secret'),
            'RedirectURI'   => config('quickbooks.redirect_uri'),
            'scope'         => 'com.intuit.quickbooks.accounting openid profile email phone address',
            'baseUrl'       => config('quickbooks.environment', 'Development'),
            'response_type' => 'code'
        ]);
    }

    /**
     * Refresh the access token
     * 
     * @param QuickBooksToken $token
     * @return array
     */
    public function refreshAccessToken(QuickBooksToken $token)
    {
        try {
            $dataService = $this->createDataService($token);
            $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
            $refreshedAccessTokenObj = $OAuth2LoginHelper->refreshToken();
            $error = $OAuth2LoginHelper->getLastError();

            if ($error) {
                $errorBody = $error->getResponseBody();
                Log::error('Error refreshing QuickBooks token: ' . $errorBody);

                if (str_contains($errorBody, 'invalid_grant')) {
                    $token->update([
                        'needs_reauth' => true,
                        'error_message' => 'Refresh token invalid or expired'
                    ]);

                    $this->clearTokenCache($token->id);
                    
                    return [
                        'success' => false,
                        'message' => 'QuickBooks refresh token is invalid. Reauthorization required.',
                        'needs_reauth' => true
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Error refreshing token: ' . $errorBody,
                    'needs_reauth' => false
                ];
            }

            // Token refreshed successfully - update in database
            $token->update([
                'access_token' => $refreshedAccessTokenObj->getAccessToken(),
                'refresh_token' => $refreshedAccessTokenObj->getRefreshToken(),
                'access_token_expires_at' => Carbon::now()->addSeconds($refreshedAccessTokenObj->getAccessTokenExpiresIn()),
                'refresh_token_expires_at' => Carbon::now()->addDays(100), // Refresh tokens expire after ~100 days
                'last_used_at' => Carbon::now(),
                'needs_reauth' => false,
                'error_message' => null
            ]);

            // Also update the DataService with the new tokens
            $dataService->updateOAuth2Token($refreshedAccessTokenObj);
            
            // Clear the cache
            $this->clearTokenCache($token->id);
            
            Log::info('QuickBooks token refreshed successfully');
            
            return [
                'success' => true,
                'message' => 'Token refreshed successfully',
                'needs_reauth' => false,
                'data_service' => $dataService
            ];
            
        } catch (\Exception $e) {
            Log::error('Exception during QuickBooks token refresh: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Exception during token refresh: ' . $e->getMessage(),
                'needs_reauth' => false
            ];
        }
    }

    /**
     * Get a valid DataService with automatic token refresh
     * 
     * @return array
     */
    public function getValidDataService()
    {
        $token = $this->getToken();
        
        if (!$token) {
            return [
                'success' => false,
                'message' => 'No QuickBooks token found. Please authorize the application.',
                'needs_reauth' => true
            ];
        }
        
        // Check if reauthorization is needed
        if ($token->needs_reauth || $token->isRefreshTokenExpired()) {
            return [
                'success' => false,
                'message' => 'QuickBooks reauthorization required. Please authorize the application.',
                'needs_reauth' => true
            ];
        }
        
        // If access token is expired or about to expire, refresh it
        if ($token->isAccessTokenExpiringSoon()) {
            $refreshResult = $this->refreshAccessToken($token);
            
            if (!$refreshResult['success']) {
                return $refreshResult;
            }
            
            // Return the updated DataService
            return [
                'success' => true,
                'message' => 'Token refreshed and DataService ready',
                'data_service' => $refreshResult['data_service']
            ];
        }
        
        // Token is valid, mark as used and return DataService
        $token->markAsUsed();
        
        return [
            'success' => true,
            'message' => 'Token is valid',
            'data_service' => $this->createDataService($token)
        ];
    }
}