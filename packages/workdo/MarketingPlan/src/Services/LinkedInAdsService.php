<?php

namespace Workdo\MarketingPlan\Services;

use GuzzleHttp\Client;

class LinkedInAdsService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.linkedin.com/v2/',
            'headers' => [
                'Authorization' => 'Bearer ' . env('LINKEDIN_ACCESS_TOKEN'),
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function createCampaign($accountId, $campaignName)
    {
        $response = $this->client->post('adCampaignsV2', [
            'json' => [
                'account' => "urn:li:sponsoredAccount:$accountId",
                'name' => $campaignName,
                'status' => 'PAUSED',
                'objectiveType' => 'WEBSITE_CONVERSIONS',
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    public function getCampaignReport($campaignId)
    {
        $response = $this->client->get("adAnalyticsV2?q=analytics&pivot=CAMPAIGN&timeGranularity=DAILY&campaigns[0]=urn:li:sponsoredCampaign:$campaignId");
        return json_decode($response->getBody(), true);
    }

    public function postCompanyUpdate($companyId, $text)
    {
        $response = $this->client->post("ugcPosts", [
            'json' => [
                'author' => "urn:li:organization:$companyId",
                'lifecycleState' => 'PUBLISHED',
                'specificContent' => [
                    'com.linkedin.ugc.ShareContent' => [
                        'shareCommentary' => [
                            'text' => $text,
                        ],
                        'shareMediaCategory' => 'NONE',
                    ],
                ],
                'visibility' => [
                    'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC',
                ],
            ],
        ]);
        return json_decode($response->getBody(), true);
    }

    public function createLeadGenForm($accountId, $formName)
    {
        $response = $this->client->post("leadGenForms", [
            'json' => [
                'account' => "urn:li:sponsoredAccount:$accountId",
                'name' => $formName,
                'status' => 'ACTIVE',
            ],
        ]);
        return json_decode($response->getBody(), true);
    }

    public function uploadVideoAd($accountId, $videoUrl)
    {
        $response = $this->client->post("videoAds", [
            'json' => [
                'account' => "urn:li:sponsoredAccount:$accountId",
                'video' => [
                    'source' => $videoUrl,
                ],
            ],
        ]);
        return json_decode($response->getBody(), true);
    }
}
