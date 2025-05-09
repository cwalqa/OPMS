<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class QuickBooksToken extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quickbooks_tokens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'realm_id',
        'access_token',
        'refresh_token',
        'access_token_expires_at',
        'refresh_token_expires_at',
        'last_used_at',
        'needs_reauth',
        'error_message',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'access_token_expires_at' => 'datetime',
        'refresh_token_expires_at' => 'datetime',
        'last_used_at' => 'datetime',
        'needs_reauth' => 'boolean',
    ];

    /**
     * Check if the access token is expired
     *
     * @return bool
     */
    public function isAccessTokenExpired()
    {
        return !$this->access_token_expires_at || Carbon::now()->greaterThan($this->access_token_expires_at);
    }

    /**
     * Check if the access token will expire soon (within 5 minutes)
     *
     * @return bool
     */
    public function isAccessTokenExpiringSoon()
    {
        return !$this->access_token_expires_at || Carbon::now()->addMinutes(5)->greaterThan($this->access_token_expires_at);
    }

    /**
     * Check if the refresh token is expired
     *
     * @return bool
     */
    public function isRefreshTokenExpired()
    {
        return !$this->refresh_token_expires_at || Carbon::now()->greaterThan($this->refresh_token_expires_at);
    }

    /**
     * Check if the refresh token will expire soon (within 5 days)
     *
     * @return bool
     */
    public function isRefreshTokenExpiringSoon()
    {
        return !$this->refresh_token_expires_at || Carbon::now()->addDays(5)->greaterThan($this->refresh_token_expires_at);
    }

    /**
     * Update the last used timestamp
     *
     * @return void
     */
    public function markAsUsed()
    {
        $this->update(['last_used_at' => Carbon::now()]);
    }
}