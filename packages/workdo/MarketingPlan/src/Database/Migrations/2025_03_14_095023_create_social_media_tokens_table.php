<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('social_media_tokens', function (Blueprint $table) {
            $table->id(); // Primary key for the credentials table
            $table->string('workspace_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User associated with the platform credentials
            $table->string('platform_name', 255)->comment('The name of the platform (e.g., Facebook, Google, LinkedIn, TikTok, Pinterest, Snapchat, Twitter, Instagram, Bing Ads, Yahoo Ads)');
            $table->string('platform_id', 255)->comment('Unique identifier for the platform account (e.g., Facebook Business ID, Google Ads Account ID, Twitter Account ID)');
            $table->string('app_id')->comment('platform App ID')->nullable();
            $table->text('access_token')->nullable()->comment('OAuth access token for the platform (nullable if not applicable)');
            $table->text('refresh_token')->nullable()->comment('OAuth refresh token used to renew access token when expired (nullable if not applicable)');
            $table->dateTime('expires_at')->nullable()->comment('Expiration date of the access token');
            $table->text('api_key')->nullable()->comment('API key used for accessing platform APIs (nullable if not applicable)');
            $table->string('client_id', 255)->nullable()->comment('OAuth client ID used for authentication (nullable if not applicable)');
            $table->string('client_secret', 255)->nullable()->comment('OAuth client secret used for authentication (nullable if not applicable)');
            $table->text('oauth_token')->nullable()->comment('OAuth 1.0a token for platforms like Twitter (nullable if not applicable)');
            $table->text('oauth_token_secret')->nullable()->comment('OAuth 1.0a token secret for platforms like Twitter (nullable if not applicable)');
            $table->string('ad_account_id', 255)->nullable()->comment('Ad account ID for platforms like Instagram Ads, Bing Ads, and Yahoo Ads');
            $table->text('scope')->nullable()->comment('The OAuth scopes granted during authentication (nullable if not applicable)');
            $table->string('access_level', 255)->nullable()->comment('The access level granted (e.g., read, write, admin)');
            $table->string('status', 50)->default('active')->comment('Status of the credentials (e.g., active, expired, revoked)');
            $table->timestamps();

            // Indexes for platform_name, user_id, and status
            $table->index('platform_name');
            $table->index('user_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_media_tokens');
    }
};
