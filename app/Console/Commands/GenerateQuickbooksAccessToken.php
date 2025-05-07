<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\QuickBooksToken;
use QuickBooksOnline\API\DataService\DataService;
use Illuminate\Support\Facades\Log;

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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $token_data =  QuickBooksToken::where('id', '1')->first();        
        
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
            self::error($error);
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

            self::info('Quickbooks access token generated successfully!');
        }
    }
}
