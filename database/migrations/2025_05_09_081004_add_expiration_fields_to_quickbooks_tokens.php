<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpirationFieldsToQuickBooksTokens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quickbooks_tokens', function (Blueprint $table) {
            // Add new fields for token expiration tracking
            $table->timestamp('access_token_expires_at')->nullable()->after('refresh_token');
            $table->timestamp('refresh_token_expires_at')->nullable()->after('access_token_expires_at');
            $table->timestamp('last_used_at')->nullable()->after('refresh_token_expires_at');
            $table->boolean('needs_reauth')->default(false)->after('last_used_at');
            $table->text('error_message')->nullable()->after('needs_reauth');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quickbooks_tokens', function (Blueprint $table) {
            $table->dropColumn([
                'access_token_expires_at',
                'refresh_token_expires_at',
                'last_used_at',
                'needs_reauth',
                'error_message'
            ]);
        });
    }
}