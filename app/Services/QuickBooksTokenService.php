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
     * Cache key for token
     */
    const CACHE_KEY = 'quickbooks_token';
    
    /**
     * Cache time in minutes
     */
    const CACHE_MINUTES = 60;
    
    /**
     * Get token from database or cache
     *
     * @return QuickBooksToken|null
     */
    public function getToken()
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_MINUTES, function () {
            return QuickBooksToken::first();
        });
    }
    
    /**
     * Clear token cache
     *
     * @return void
     */
    public function clearTokenCache()
    {
        Cache::forget(self::CACHE_KEY);
    }
    
    /**
     * Create auth service for initial authorization
     *
     * @return DataService
     */
    public function createAuthService()
    {
        return DataService::Configure([
            'auth_mode'    => 'oauth2',
            'ClientID'     => config('quickbooks.client_id'),
            'ClientSecret' => config('quickbooks.client_secret'),
            'RedirectURI'  => config('quickbooks.redirect_uri'),
            'scope'        => config('quickbooks.scope', 'com.intuit.quickbooks.accounting openid profile email phone address'),
            'baseUrl'      => config('quickbooks.environment', 'Development'),
        ]);
    }
    
    /**
     * Create a DataService instance for API calls
     *
     * @param QuickBooksToken $token
     * @return DataService
     */
    public function createDataService(QuickBooksToken $token)
    {
        return DataService::Configure([
            'auth_mode'    => 'oauth2',
            'ClientID'     => config('quickbooks.client_id'),
            'ClientSecret' => config('quickbooks.client_secret'),
            'RedirectURI'  => config('quickbooks.redirect_uri'),
            'baseUrl'      => config('quickbooks.environment', 'Development'),
            'accessTokenKey' => $token->access_token,
            'refreshTokenKey' => $token->refresh_token,
            'QBORealmID' => $token->realm_id
        ]);
    }
    
    /**
     * Get a valid DataService for making API calls
     * Refreshes token if necessary
     *
     * @return array
     */
    public function getValidDataService()
    {
        $token = $this->getToken();
        
        if (!$token) {
            return [
                'success' => false,
                'needs_reauth' => true,
                'message' => 'No QuickBooks connection found.'
            ];
        }
        
        if ($token->needs_reauth || $token->isRefreshTokenExpired()) {
            return [
                'success' => false,
                'needs_reauth' => true,
                'message' => 'QuickBooks connection requires reauthorization.'
            ];
        }
        
        if ($token->isAccessTokenExpiringSoon()) {
            $refreshResult = $this->refreshAccessToken($token);
            
            if (!$refreshResult['success']) {
                return $refreshResult;
            }
            
            // Get fresh token after refresh
            $this->clearTokenCache();
            $token = $this->getToken();
        }
        
        try {
            $dataService = $this->createDataService($token);
            
            // Mark token as used
            $token->update(['last_used_at' => Carbon::now()]);
            
            return [
                'success' => true,
                'data_service' => $dataService
            ];
        } catch (\Exception $e) {
            Log::error('Error creating QuickBooks DataService: ' . $e->getMessage());
            
            return [
                'success' => false,
                'needs_reauth' => false,
                'message' => 'Error creating QuickBooks connection: ' . $e->getMessage()
            ];
        }
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
            $dataService = DataService::Configure([
                'auth_mode'    => 'oauth2',
                'ClientID'     => config('quickbooks.client_id'),
                'ClientSecret' => config('quickbooks.client_secret'),
                'RedirectURI'  => config('quickbooks.redirect_uri'),
                'scope'        => config('quickbooks.scope', 'com.intuit.quickbooks.accounting openid profile email phone address'),
                'baseUrl'      => config('quickbooks.environment', 'Development'),
                'refreshTokenKey' => $token->refresh_token
            ]);
            
            $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
            $accessTokenObj = $OAuth2LoginHelper->refreshAccessTokenWithRefreshToken($token->refresh_token);
            
            if (!$accessTokenObj) {
                throw new \Exception('Failed to refresh access token');
            }
            
            $accessToken = $accessTokenObj->getAccessToken();
            $refreshToken = $accessTokenObj->getRefreshToken();
            
            // Get token expiration - handle different SDK versions
            $accessTokenExpiresAt = Carbon::now()->addHour(); // Default fallback is 1 hour
            
            if (method_exists($accessTokenObj, 'getAccessTokenExpiresAt')) {
                $accessTokenExpiresAt = Carbon::parse($accessTokenObj->getAccessTokenExpiresAt());
            } elseif (method_exists($accessTokenObj, 'getExpiresAt')) {
                $accessTokenExpiresAt = Carbon::parse($accessTokenObj->getExpiresAt());
            } elseif (method_exists($accessTokenObj, 'getExpiresIn')) {
                $accessTokenExpiresAt = Carbon::now()->addSeconds($accessTokenObj->getExpiresIn());
            } elseif (method_exists($accessTokenObj, 'getAccessTokenExpiresIn')) {
                $accessTokenExpiresAt = Carbon::now()->addSeconds($accessTokenObj->getAccessTokenExpiresIn());
            } elseif (property_exists($accessTokenObj, 'expires_in')) {
                $accessTokenExpiresAt = Carbon::now()->addSeconds($accessTokenObj->expires_in);
            }
            
            // Update token in database
            $token->update([
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'access_token_expires_at' => $accessTokenExpiresAt,
                'last_used_at' => Carbon::now(),
                'needs_reauth' => false,
                'error_message' => null
            ]);
            
            // Clear the cache so next request gets fresh token
            $this->clearTokenCache();
            
            Log::info('QuickBooks access token refreshed successfully.');
            
            return [
                'success' => true,
                'message' => 'Access token refreshed successfully.'
            ];
        } catch (\Exception $e) {
            Log::error('Error refreshing QuickBooks token: ' . $e->getMessage());
            
            // Check if token needs reauthorization
            $needsReauth = false;
            $errorMsg = $e->getMessage();
            
            // Check common error messages that indicate the need for reauthorization
            if (
                stripos($errorMsg, 'invalid_grant') !== false ||
                stripos($errorMsg, 'refresh token is invalid') !== false ||
                stripos($errorMsg, 'refresh token expired') !== false ||
                stripos($errorMsg, 'invalid refresh token') !== false
            ) {
                $needsReauth = true;
                $token->update([
                    'needs_reauth' => true,
                    'error_message' => $errorMsg
                ]);
            }
            
            return [
                'success' => false,
                'needs_reauth' => $needsReauth,
                'message' => 'Failed to refresh token: ' . $errorMsg
            ];
        }
    }
}