<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2LoginHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\QuickBooksToken;

class GenerateQuickbooksAccessToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quickbooks:generate-access-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate QuickBooks Access Token';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->info('Generating QuickBooks Access Token...');
            
            // Get the DataService configured for OAuth2
            $dataService = DataService::Configure([
                'auth_mode' => 'oauth2',
                'ClientID' => config('quickbooks.client_id'),
                'ClientSecret' => config('quickbooks.client_secret'),
                'RedirectURI' => config('quickbooks.redirect_uri'),
                'scope' => config('quickbooks.scope', 'com.intuit.quickbooks.accounting openid profile email phone address'),
                'baseUrl' => config('quickbooks.base_url', config('quickbooks.environment', 'Development'))
            ]);
            
            // Check if we have refresh token stored in database or file
            $refreshToken = $this->getRefreshToken();
            
            if ($refreshToken) {
                $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
                
                try {
                    $accessTokenObj = $OAuth2LoginHelper->refreshAccessTokenWithRefreshToken($refreshToken);
                    
                    // Check if we have a valid access token object
                    if (!$accessTokenObj) {
                        throw new \Exception("Failed to refresh access token. Received null response.");
                    }
                    
                    // Get the updated access token and refresh token
                    $accessToken = $accessTokenObj->getAccessToken();
                    $refreshToken = $accessTokenObj->getRefreshToken();
                    
                    // Get token expiry - use a fallback of 1 hour from now
                    $expiresAt = Carbon::now()->addHour(); // Default fallback
                    
                    // Check if we have access to expiration data via different methods
                    // Try different approaches to get expiration information
                    if (method_exists($accessTokenObj, 'getAccessTokenExpiresAt')) {
                        $expiresAt = Carbon::parse($accessTokenObj->getAccessTokenExpiresAt());
                    } elseif (method_exists($accessTokenObj, 'getRefreshTokenExpiresAt')) {
                        $expiresAt = Carbon::parse($accessTokenObj->getRefreshTokenExpiresAt());
                    } elseif (method_exists($accessTokenObj, 'getExpiresAt')) {
                        $expiresAt = Carbon::parse($accessTokenObj->getExpiresAt());
                    } elseif (method_exists($accessTokenObj, 'getExpiresIn')) {
                        $expiresAt = Carbon::now()->addSeconds($accessTokenObj->getExpiresIn());
                    } elseif (property_exists($accessTokenObj, 'expires_in')) {
                        $expiresAt = Carbon::now()->addSeconds($accessTokenObj->expires_in);
                    } elseif (property_exists($accessTokenObj, 'expires_at')) {
                        $expiresAt = Carbon::parse($accessTokenObj->expires_at);
                    }
                    
                    // Store the updated tokens
                    $this->storeTokens($accessToken, $refreshToken, $expiresAt->toDateTimeString());
                    
                    // Update the QuickBooksToken model as well if it exists
                    try {
                        $token = QuickBooksToken::first();
                        if ($token) {
                            $token->update([
                                'access_token' => $accessToken,
                                'refresh_token' => $refreshToken,
                                'access_token_expires_at' => $expiresAt,
                                'last_used_at' => Carbon::now(),
                                'needs_reauth' => false,
                                'error_message' => null
                            ]);
                            $this->line('Updated QuickBooksToken model record.');
                        }
                    } catch (\Exception $e) {
                        $this->warn('Could not update QuickBooksToken model: ' . $e->getMessage());
                    }
                    
                    $this->info('QuickBooks access token has been successfully refreshed.');
                } catch (\Exception $e) {
                    $this->error('Error during token refresh: ' . $e->getMessage());
                    
                    // Log detailed information for debugging
                    Log::error('QuickBooks token refresh failed: ' . $e->getMessage());
                    
                    // Update token status to indicate it needs reauthorization
                    try {
                        $token = QuickBooksToken::first();
                        if ($token) {
                            $token->update([
                                'needs_reauth' => true,
                                'error_message' => $e->getMessage()
                            ]);
                            $this->line('Updated token status to require reauthorization.');
                        }
                    } catch (\Exception $modelEx) {
                        $this->warn('Could not update token status: ' . $modelEx->getMessage());
                    }
                    
                    return 1;
                }
            } else {
                $this->error('No refresh token found. Please authorize the application first.');
                return 1;
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('GenerateQuickbooksAccessToken error: ' . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Get the refresh token from the primary database table or fallback sources.
     *
     * @return string|null
     */
    private function getRefreshToken()
    {
        try {
            $token = QuickBooksToken::first(); // Or use ->where('realm_id', $id)->first()
            if ($token && !empty($token->refresh_token)) {
                $this->line('Refresh token loaded from QuickBooksToken model.');
                return $token->refresh_token;
            }
        } catch (\Exception $e) {
            $this->warn('Could not query QuickBooksToken model: ' . $e->getMessage());
        }

        // Try database settings table
        try {
            $setting = DB::table('settings')->where('key', 'quickbooks_refresh_token')->first();
            if ($setting && !empty($setting->value)) {
                $this->line('Refresh token loaded from settings table.');
                return $setting->value;
            }
        } catch (\Exception $e) {
            $this->warn('Could not query settings table: ' . $e->getMessage());
        }

        // Try to get from .env (legacy support)
        if ($envToken = env('QUICKBOOKS_REFRESH_TOKEN')) {
            $this->line('Refresh token loaded from .env.');
            return $envToken;
        }

        // Try to get from JSON file in storage
        if (Storage::exists('quickbooks/tokens.json')) {
            $tokens = json_decode(Storage::get('quickbooks/tokens.json'), true);
            if (isset($tokens['refresh_token']) && !empty($tokens['refresh_token'])) {
                $this->line('Refresh token loaded from tokens.json.');
                return $tokens['refresh_token'];
            }
        }

        $this->error('No valid refresh token found from any source.');
        return null;
    }
    
    /**
     * Store tokens in available storage options
     * 
     * @param string $accessToken
     * @param string $refreshToken
     * @param string $expiresAt
     * @return void
     */
    private function storeTokens($accessToken, $refreshToken, $expiresAt)
    {
        // Try to store in database settings table if it exists
        try {
            DB::table('settings')->updateOrInsert(
                ['key' => 'quickbooks_access_token'],
                ['value' => $accessToken]
            );
            
            DB::table('settings')->updateOrInsert(
                ['key' => 'quickbooks_refresh_token'],
                ['value' => $refreshToken]
            );
            
            DB::table('settings')->updateOrInsert(
                ['key' => 'quickbooks_token_expires_at'],
                ['value' => $expiresAt]
            );
            
            $this->line('Tokens stored in settings table.');
        } catch (\Exception $e) {
            $this->warn('Could not store tokens in settings table: ' . $e->getMessage());
        }
        
        // Store in file as a backup
        $tokens = [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_at' => $expiresAt
        ];
        
        try {
            Storage::put('quickbooks/tokens.json', json_encode($tokens, JSON_PRETTY_PRINT));
            $this->line('Tokens stored in JSON file.');
        } catch (\Exception $e) {
            $this->warn('Could not store tokens in JSON file: ' . $e->getMessage());
        }
        
        $this->info('Tokens stored successfully.');
    }
}