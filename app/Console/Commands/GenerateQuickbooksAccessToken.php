<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2LoginHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
                'scope' => config('quickbooks.scope'),
                'baseUrl' => config('quickbooks.base_url')
            ]);
            
            // Check if we have refresh token stored in database or file
            $refreshToken = $this->getRefreshToken();
            
            if ($refreshToken) {
                $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
                $accessTokenObj = $OAuth2LoginHelper->refreshToken($refreshToken);
                
                // Get the updated access token and refresh token
                $accessToken = $accessTokenObj->getAccessToken();
                $refreshToken = $accessTokenObj->getRefreshToken();
                
                // Get token expiry - use a fallback of 1 hour from now
                $expiresAt = Carbon::now()->addHour(); // Default fallback
                
                if (method_exists($accessTokenObj, 'getAccessTokenExpiresAt')) {
                    $expiresAt = Carbon::parse($accessTokenObj->getAccessTokenExpiresAt());
                } elseif (method_exists($accessTokenObj, 'getRefreshTokenExpiresAt')) {
                    $expiresAt = Carbon::parse($accessTokenObj->getRefreshTokenExpiresAt());
                }
                
                // Store the updated tokens
                $this->storeTokens($accessToken, $refreshToken, $expiresAt->toDateTimeString());
                
                $this->info('QuickBooks access token has been successfully refreshed.');
            } else {
                $this->error('No refresh token found.');
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Get the refresh token from available storage options
     * 
     * @return string|null
     */
    private function getRefreshToken()
    {
        // Try to get from database settings table if it exists
        try {
            $setting = DB::table('settings')->where('key', 'quickbooks_refresh_token')->first();
            if ($setting) {
                return $setting->value;
            }
        } catch (\Exception $e) {
            // Table might not exist, continue to next method
        }
        
        // Try to get from env
        if (env('QUICKBOOKS_REFRESH_TOKEN')) {
            return env('QUICKBOOKS_REFRESH_TOKEN');
        }
        
        // Try to get from storage file
        if (Storage::exists('quickbooks/tokens.json')) {
            $tokens = json_decode(Storage::get('quickbooks/tokens.json'), true);
            if (isset($tokens['refresh_token'])) {
                return $tokens['refresh_token'];
            }
        }
        
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
        } catch (\Exception $e) {
            // Table might not exist, continue to next method
        }
        
        // Store in file as a backup
        $tokens = [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_at' => $expiresAt
        ];
        
        Storage::put('quickbooks/tokens.json', json_encode($tokens, JSON_PRETTY_PRINT));
        
        // Log success
        $this->info('Tokens stored successfully.');
    }
}