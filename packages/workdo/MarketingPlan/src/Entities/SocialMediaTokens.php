<?php

namespace Workdo\MarketingPlan\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMediaTokens extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'workspace_id', // Workspace associated with the platform credentials
        'user_id', // User associated with the platform credentials
        'platform_name', // Name of the platform (e.g., Facebook, Google, LinkedIn, etc.)
        'platform_id', // Unique identifier for the platform account
        'app_id', // Platform App ID (nullable)
        'access_token', // OAuth access token (nullable)
        'refresh_token', // OAuth refresh token (nullable)
        'expires_at', // Expiration date of the access token (nullable)
        'api_key', // API key used for accessing platform APIs (nullable)
        'client_id', // OAuth client ID used for authentication (nullable)
        'client_secret', // OAuth client secret used for authentication (nullable)
        'oauth_token', // OAuth 1.0a token (nullable)
        'oauth_token_secret', // OAuth 1.0a token secret (nullable)
        'ad_account_id', // Ad account ID (nullable)
        'scope', // OAuth scopes granted (nullable)
        'access_level', // Access level granted (e.g., read, write, admin) (nullable)
        'status', // Status of the credentials (active, expired, revoked)
    ];

}
