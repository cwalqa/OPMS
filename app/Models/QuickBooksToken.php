<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuickBooksToken extends Model
{
    use HasFactory;

    protected $table="quickbooks_tokens";
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'refresh_token',
        'access_token',
        'realm_id',
        'created_at',
        'updated_at',
    ];
}
