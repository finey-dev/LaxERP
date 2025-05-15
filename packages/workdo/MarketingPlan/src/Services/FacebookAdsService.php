<?php

namespace Workdo\MarketingPlan\Services;

use AWS\CRT\HTTP\Request;
use Exception;
use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Business;
use FacebookAds\Object\Fields\AdAccountFields;
use FacebookAds\Object\Fields\AdFields;
use FacebookAds\Object\Fields\AdsInsightsFields;
use FacebookAds\Object\Fields\LeadFields;
use FacebookAds\Object\Fields\LeadgenFormFields;
use FacebookAds\Object\Fields\PageFields;
use FacebookAds\Object\Fields\UserFields;
use FacebookAds\Object\Lead;
use FacebookAds\Object\Page;
use FacebookAds\Object\InstagramUser;
use FacebookAds\Object\ProductCatalog;
use FacebookAds\Object\LeadgenForm;
use FacebookAds\Object\EventSourceGroup;
use FacebookAds\Object\Campaign;
use FacebookAds\Object\AdSet;
use FacebookAds\Object\Ad;
use FacebookAds\Object\AdCreative;
use FacebookAds\Object\AdImage;
use FacebookAds\Object\CustomAudience;
use FacebookAds\Object\Fields\AdCreativeFields;
use FacebookAds\Object\Fields\AdImageFields;
use FacebookAds\Object\Fields\AdSetFields;
use FacebookAds\Object\Fields\CampaignFields;
use FacebookAds\Object\Fields\CustomAudienceFields;
use FacebookAds\Object\Fields\InstagramMediaFields;
use FacebookAds\Object\InstagramMedia;
use FacebookAds\Object\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Workdo\MarketingPlan\Entities\SocialMediaTokens;

class FacebookAdsService
{
    protected $api;
    protected $accountId;

    public function __construct($accountId = null)
    {

        $this->api = Api::init(
            config('services.facebook.app_id'),
            config('services.facebook.app_secret'),
            config('services.facebook.access_token')
        );

        $this->accountId = $accountId ?: 'act_' . config('services.facebook.account_id');
    }

    private function handleAccessToken()
    {
        return $this->handleErrors(function () {
            $record = $this->getAccessTokenFromDb();
            $accessToken = $record->access_token;
            $expirationTime = $record->expiration_time;

            return $this->isAccessTokenExpired($expirationTime)
                ? $this->refreshAccessToken($accessToken)
                : $accessToken;
        });

    }

    public function getUser()
    {
        return $this->handleErrors(function () {
            $response = $this->api->call(
                '/me',
                'GET',
                ['fields' => 'id,name,email,gender,birthday,location,picture.type(large)']
            );
            return $response->getContent();
        });
    }


    /**
     * Refresh the access token using the refresh token flow
     * @throws ConnectionException
     */
    private function refreshAccessToken($expiredAccessToken)
    {
        // Facebook API endpoint to refresh access token
        $url = 'https://graph.facebook.com/v22.0/oauth/access_token';
        $params = [
            'grant_type' => 'fb_exchange_token',
            'client_id' => config('services.facebook.app_id'),
            'client_secret' => config('services.facebook.app_secret'),
            'fb_exchange_token' => $expiredAccessToken
        ];

        // Make the request to get a new token
        return Http::get($url, $params)->json(); // Return the new token data
    }

    /**
     * Error handling wrapper for API calls.
     */
    public function handleErrors($function)
    {
        try {
            return $function();
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // ----------------- Marketing API Access -----------------

    /**
     * Create an ad campaign.
     */
    public function createCampaign($name, $objective, $status = 'PAUSED', $budget = null, $budgetType = 'daily', $bidStrategy = 'LOWEST_COST')
    {
        return $this->handleErrors(function() use ($name, $objective, $status, $budget, $budgetType, $bidStrategy) {
            $adAccount = $this->getAdAccount($this->accountId);

            $campaignData = array_merge([
                CampaignFields::NAME => $name,
                CampaignFields::OBJECTIVE => $objective,
                CampaignFields::STATUS => $status,
                CampaignFields::BUYING_TYPE => 'AUCTION',  // This is typical for CBO campaigns
                CampaignFields::BID_STRATEGY => $bidStrategy,
            ],$this->setBudgetData($budget, $budgetType));

            return $adAccount->createCampaign($campaignData);
        });
    }

    /**
     * Update campaign details like labels and spend cap.
     */
    public function updateCampaign($campaignId, $labels = [], $spendCap = null)
    {
        return $this->handleErrors(function() use ($campaignId, $labels, $spendCap) {
            $campaign = new Campaign($campaignId);
            $updateData = [];

            if (!empty($labels)) {
                $updateData[CampaignFields::ADLABELS] = $labels;
            }

            if ($spendCap !== null) {
                $updateData[CampaignFields::SPEND_CAP] = $spendCap;
            }

            $campaign->setData($updateData);
            return $campaign->updateSelf();
        });
    }

    public function getAdAccount($adAccountId)
    {
        return $this->handleErrors(function() use ($adAccountId) {
            return new AdAccount($adAccountId);
        });

    }

    /**
     * Create an ad set within a campaign.
     */
    public function createAdSet($campaignId, $name, $dailyBudget, array $targeting, $budgetType = 'daily')
    {
        return $this->handleErrors(function() use ($budgetType, $campaignId, $name, $dailyBudget, $targeting) {
            $adAccount = $this->getAdAccount($this->accountId);
            $fields = array_merge([
                AdSetFields::NAME => $name,
                AdSetFields::CAMPAIGN_ID => $campaignId,
                AdSetFields::TARGETING => $targeting,
                AdSetFields::START_TIME => date('c'),
                AdSetFields::END_TIME => date('c', strtotime('+1 month')),
                AdSetFields::OPTIMIZATION_GOAL => 'REACH',
            ], $this->setBudgetData($dailyBudget, $budgetType));

            return $adAccount->createAdSet($fields);
        });
    }

    /**
     * Update the budget of an existing ad set.
     *
     * @param string $budgetType ('daily' or 'lifetime')
     */
    public function updateAdSetBudget($adSetId, $newBudget, $budgetType = 'daily')
    {
        return $this->handleErrors(function() use ($adSetId, $newBudget, $budgetType) {
            $adSet = new AdSet($adSetId);
            $budgetField = $budgetType === 'daily' ? AdSetFields::DAILY_BUDGET : AdSetFields::LIFETIME_BUDGET;
            $adSet->setData([$budgetField => $newBudget]);

            return $adSet;
        });
    }

    private function setBudgetData($budget, $budgetType): array
    {
        $budgetField = $budgetType === 'daily' ? AdSetFields::DAILY_BUDGET : AdSetFields::LIFETIME_BUDGET;
        return [$budgetField => $budget];
    }



    /**
     * Schedule an ad.
     */
    public function scheduleAd($adId, $startTime, $endTime)
    {
        return $this->handleErrors(function() use ($adId, $startTime, $endTime) {
            $ad = new Ad($adId);
            $ad->setData([
                AdFields::AD_SCHEDULE_START_TIME => $startTime,
                AdFields::AD_SCHEDULE_END_TIME => $endTime,
            ]);
            return $ad->updateSelf();
        });
    }

    /**
     * Fetch campaign insights, including remaining budget.
     */
    public function getIndividualCampaignInsights($campaignId, $fields = [], $params = [])
    {
        return $this->handleErrors(function() use ($campaignId, $fields, $params) {
            $campaign = new Campaign($campaignId);

            // Default fields if none are specified
            if (empty($fields)) {
                $fields = [
                    'impressions',
                    'clicks',
                    'spend',
                    'ctr',
                    'conversions',
                    CampaignFields::BUDGET_REMAINING,  // Add remaining budget
                ];
            }

            $insights = $campaign->getInsights($fields, $params);

            // Include the budget remaining as part of the insights if not already there
            $campaign->getSelf([CampaignFields::BUDGET_REMAINING]);
            $insights['budget_remaining'] = $campaign->{CampaignFields::BUDGET_REMAINING};

            return $insights;
        });
    }

    public function getTableCampaignInsights()
    {
        return $this->handleErrors(function() {
            $campaigns = $this->listAdAccountCampaigns();
            $insights = [];
            foreach ($campaigns as $campaign) {
                $campaignId = $campaign->{CampaignFields::ID};
                $data = new Campaign($campaignId);
                $insights[] = $data->getSelf([
                    CampaignFields::ID,
                    CampaignFields::NAME,
                    CampaignFields::STATUS,
                    CampaignFields::OBJECTIVE,
                    CampaignFields::BUDGET_REMAINING,
                    CampaignFields::SPEND_CAP,
                    CampaignFields::START_TIME,
                    CampaignFields::STOP_TIME,
                ]);
            }
            return $insights;
        });
    }

    /*
     * Fetch All Campaign Insights
     */
    public function getAllCampaignInsights($startTime = null, $endTime = null, $date_preset = 'last_30d')
    {
        return $this->handleErrors(function() use ($startTime, $endTime, $date_preset) {
            $fields = [
                AdsInsightsFields::AD_NAME,
                AdsInsightsFields::ADSET_ID,
                AdsInsightsFields::CAMPAIGN_ID,
                AdsInsightsFields::IMPRESSIONS,
                AdsInsightsFields::CLICKS,
                AdsInsightsFields::SPEND,
                AdsInsightsFields::REACH,
                AdsInsightsFields::CTR,
                AdsInsightsFields::ACTIONS,
            ];

            $params = ['level' => 'ad', 'limit' => 100];
            $params[$startTime && $endTime ? 'time_range' : 'date_preset'] = $startTime && $endTime ? ['since' => $startTime, 'until' => $endTime] : $date_preset;

            $adAccount = new AdAccount($this->accountId);
            $insights = $adAccount->getInsights($fields, $params);

            $adData = [];
            $conversions = [];

            $totals = [
                'impressions' => 0,
                'clicks' => 0,
                'spend' => 0.0,
                'reach' => 0,
                'ctr' => 0.0,
                'budget' => 0.0,
                'budget_remaining' => 0.0,
                'count' => 0, // For averaging CTR
            ];

            foreach ($insights as $insight) {
                $adSetId = $insight->{AdsInsightsFields::ADSET_ID} ?? null;
                $budget = 0.0;
                $budgetRemaining = 0.0;

                if ($adSetId !== null) {
                    try {
                        $adSet = new AdSet($adSetId);
                        $adSetData = $adSet->read([
                            AdSetFields::DAILY_BUDGET,
                            AdSetFields::LIFETIME_BUDGET,
                            AdSetFields::BUDGET_REMAINING
                        ]);
                        $budget = ($adSetData->{AdSetFields::DAILY_BUDGET} ?? $adSetData->{AdSetFields::LIFETIME_BUDGET} ?? 0) / 100;
                        $budgetRemaining = ($adSetData->{AdSetFields::BUDGET_REMAINING} ?? 0) / 100;
                    } catch (\Exception $e) {
                        Log::error('Error fetching budget for AdSet ID: ' . $adSetId . ' in Ad Account: ' . $this->accountId);
                        Log::error($e);
                    }
                }

                $adData[$adSetId] = [
                    'ad_name' => $insight->{AdsInsightsFields::AD_NAME} ?? 'N/A',
                    'impressions' => (int) (($insight->{AdsInsightsFields::IMPRESSIONS} ?? 0)),
                    'clicks' => (int) ($insight->{AdsInsightsFields::CLICKS} ?? 0),
                    'spend' => (float) ($insight->{AdsInsightsFields::SPEND} ?? 0.0),
                    'reach' => (int) ($insight->{AdsInsightsFields::REACH} ?? 0),
                    'ctr' => (float) ($insight->{AdsInsightsFields::CTR} ?? 0.0),
                    'budget' => $budget ?? 0,
                    'budget_remaining' => $budgetRemaining ?? 0,
                ];

                // Process Conversions
                if (isset($insight->{AdsInsightsFields::ACTIONS})) {
                    foreach ($insight->{AdsInsightsFields::ACTIONS} as $action) {
                        $conversions[] = [
                            'ad_name' => $insight->{AdsInsightsFields::AD_NAME} ?? 'N/A',
                            'action_type' => $action['action_type'] ?? 'Unknown',
                            'value' => $action['value'] ?? 0,
                        ];
                    }
                }

                // Add to totals
                $totals['impressions'] += $adData[$adSetId]['impressions'];
                $totals['clicks'] += $adData[$adSetId]['clicks'];
                $totals['spend'] += $adData[$adSetId]['spend'];
                $totals['reach'] += $adData[$adSetId]['reach'];
                $totals['budget'] += $budget;
                $totals['budget_remaining'] += $budgetRemaining;
                if (isset($insight->{AdsInsightsFields::CTR})) {
                    $totals['ctr'] += (float) $insight->{AdsInsightsFields::CTR};
                    $totals['count']++;
                }
            }

            $agePerformance = $this->fetchBreakdown('age', $params);
            $locationPerformance = $this->fetchBreakdown('region', $params);
            $devicePerformance = $this->fetchBreakdown('device_platform', $params);

            // Average CTR Calculation
            $totals['ctr'] = $totals['count'] > 0 ? ($totals['ctr'] / $totals['count']) : 0.0;

            return [
                'adData' => $adData,
                'topAds' => '',
                'totals' => $totals,
                'conversions' => array_sum(array_column($conversions, 'value')),
                'agePerformance' => $agePerformance,
                'locationPerformance' => $locationPerformance,
                'devicePerformance' => $devicePerformance,
                'metadata' => [
                    'account_id' => $this->accountId,
                    'date_range' => $startTime && $endTime ? "$startTime to $endTime" : $date_preset,
                ],
            ];
        });
    }

    private function fetchBreakdown($breakdown, $params): array
    {
        $params['breakdowns'] = [$breakdown];
        $fields = [
            AdsInsightsFields::IMPRESSIONS,
            AdsInsightsFields::CLICKS,
            AdsInsightsFields::SPEND,
            AdsInsightsFields::REACH,
        ];

        $adAccount = new AdAccount($this->accountId);
        $insights = $adAccount->getInsights($fields, $params);

        $data = [];
        foreach ($insights as $insight) {
            $data[] = [
                $breakdown => $insight->{$breakdown} ?? 'Unknown',
                'impressions' => (int) (($insight->{AdsInsightsFields::IMPRESSIONS} ?? 0)),
                'clicks' => (int) $insight->{AdsInsightsFields::CLICKS} ?? 0,
                'spend' => (float) $insight->{AdsInsightsFields::SPEND} ?? 0.0,
                'reach' => (int) $insight->{AdsInsightsFields::REACH} ?? 0,
            ];
        }

        return $data;
    }

    // ----------------- Audience Management -----------------

    /**
     * Create a custom audience.
     */
    public function createCustomAudience($name, $description)
    {
        return $this->handleErrors(function() use ($name, $description) {
            $audience = new CustomAudience(null, $this->accountId);
            $audience->setData([
                CustomAudienceFields::NAME => $name,
                CustomAudienceFields::DESCRIPTION => $description,
                CustomAudienceFields::SUBTYPE => 'WEBSITE',
            ]);
            return $audience->create();
        });
    }

    /**
     * Create a lookalike audience.
     */
    public function createLookalikeAudience($originAudienceId, $country)
    {
        return $this->handleErrors(function() use ($originAudienceId, $country) {
            $audience = new CustomAudience(null, $this->accountId);
            $audience->setData([
                CustomAudienceFields::NAME => 'Lookalike Audience',
                CustomAudienceFields::ORIGIN_AUDIENCE_ID => $originAudienceId,
                CustomAudienceFields::LOOKALIKE_SPEC => [
                    'country' => $country,
                    'ratio' => 0.01,
                ],
            ]);
            return $audience->create();
        });
    }

    // ----------------- Ad Creative Management -----------------

    /**
     * Create an ad creative.
     */
    public function createAdCreative($name, $imageUrl, $linkUrl)
    {
        return $this->handleErrors(function() use ($name, $imageUrl, $linkUrl) {
            $imageHash = $this->uploadImage($imageUrl);
            $creative = new AdCreative(null, $this->accountId);
            $creative->setData([
                AdCreativeFields::NAME => $name,
                AdCreativeFields::OBJECT_STORY_SPEC => [
                    'page_id' => config('services.facebook.page_id'),
                    'link_data' => [
                        'link' => $linkUrl,
                        'image_hash' => $imageHash,
                    ],
                ],
            ]);
            return $creative->create();
        });
    }

    /**
     * Upload an image and return its hash.
     */
    private function uploadImage($imageUrl)
    {
        return $this->handleErrors(function() use ($imageUrl) {
            $image = new AdImage(null, $this->accountId);
            $image->setData([
                AdImageFields::FILENAME => $imageUrl
            ]);
            $image->create();
            return $image->{AdImageFields::HASH};
        });
    }

    // ----------------- Instagram API -----------------

    /**
     * Retrieve Instagram account insights.
     */
    public function getInstagramInsights($instagramAccountId, $metrics = [])
    {
        return $this->handleErrors(function() use ($instagramAccountId, $metrics) {
            return (new InstagramUser($instagramAccountId))->getInsights($metrics);
        });
    }

    /**
     * Publish a post to Instagram.
     */
    public function publishInstagramPost($instagramAccountId, $imageUrl, $caption)
    {
        return $this->handleErrors(function() use ($instagramAccountId, $imageUrl, $caption) {
            $instagramMedia = new InstagramMedia(null, $instagramAccountId);
            $instagramMedia->setData([
//                InstagramMediaFields::IMAGE_URL => $imageUrl,
                InstagramMediaFields::DISPLAY_URL => $imageUrl,
                InstagramMediaFields::CAPTION_TEXT => $caption,
            ]);
            return $instagramMedia->create();
        });
    }

    /**
     * Retrieve engagement metrics for an Instagram post.
     */
    public function getInstagramEngagementMetrics($mediaId)
    {
        return $this->handleErrors(function() use ($mediaId) {
            return (new InstagramMedia($mediaId))->getInsights(['likes', 'comments', 'reach']);
        });
    }

    // ----------------- Lead Ads & Offline Events -----------------

    /**
     * Get leads from a Facebook page.
     */
    public function getLeadForms($pageId)
    {
        return $this->handleErrors(function() use ($pageId) {
            return (new Page($pageId))->getLeadgenForms();
        });
    }

    /**
     * Fetch all Facebook Pages linked to the user
     */
    public function getAllPages(): array
    {
        try {
            // Fetch the pages that the user has admin access to
            $response = $this->api->call('/me/accounts', 'GET', [
                'access_token' => config('services.facebook.access_token')
            ]);

            // Get the data from the response
            $pagesData = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);


            $pages = [];

            // Loop through the data to extract page IDs and other information
            if (isset($pagesData['data'])) {
                foreach ($pagesData['data'] as $page) {
                    $pages[] = [
                        'id' => $page['id'],
                        'name' => $page['name'],
                        'access_token' => $page['access_token'],
                        'category' => $page['category'],
                    ];
                }
            }

            return $pages;
        } catch (\Exception $e) {
            // Handle any errors that may occur during the API request
            return ['error' => "Error fetching user-owned pages: " . $e->getMessage()];
        }
    }

    /**
     * Fetch all ad accounts associated with the user.
     *
     * @return array
     */


    public function getAdAccounts()
    {
//        return $this->handleErrors(function() {
           $response = $this->getUser();
           $userId = $response['id'];

            return (new User($userId))->getAdAccounts([
                AdAccountFields::NAME,
                AdAccountFields::ID,
                AdAccountFields::ACCOUNT_STATUS
            ]);
//        });
    }

    public function getAdsFromAdAccounts()
    {
        return $this->handleErrors(function() {
            $adAccounts = $this->getAdAccounts();
            $ads = [];
            foreach ($adAccounts as $adAccount) {
                $adAccount = new AdAccount($adAccount->{AdAccountFields::ID});

                $ads[] = $adAccount->getAds([
                    AdFields::NAME,
                    AdFields::ID,
                    AdFields::STATUS,
                    AdFields::ADSET_ID,
                ]);
            }
            return $ads;
        });
    }

    public function getLeadsFromAds()
    {
        return $this->handleErrors(function() {
            $ads = $this->getAdsFromAdAccounts();
            $leads = [];
            foreach ($ads as $ad) {
                $ad = new Ad($ad->{AdFields::ID});
                $lead = $ad->getLeads(
                    [
                    LeadFields::ID,
                    LeadFields::AD_ID,
                    LeadFields::AD_NAME,
                    LeadFields::CAMPAIGN_ID,
                    LeadFields::CAMPAIGN_NAME,
                    LeadFields::CREATED_TIME,
                    LeadFields::IS_ORGANIC,
                ], [], config('services.facebook.access_token'));

                $leads[] = json_decode($lead->getLastResponse()->getBody(), true, 512, JSON_THROW_ON_ERROR);
            }

            return $leads;
        });
    }

    public function getTotalLeads()
    {
        return 0;
    }


/*
 * todo check and delete the following code
 */

//    /**
//     * Fetch all lead forms from multiple pages
//     */
//    public function getLeadFormsFromPages(): array
//    {
//        $pages = $this->getAllPages();
//
//        $forms = [];
//        foreach ($pages as $page) {
//            try {
//                $pageObject = new Page($page['id']);
//
//                $params = [
//                    'fields' => 'id,name,status,created_time',  // Specify which fields you need
//                    // 'limit'  => 10,  Limit to 10 forms per page (optional)
//                    'status' => 'ACTIVE',  // Only fetch active forms
//                ];
//
//                $leadForms = $pageObject->getLeadGenForms([], [], $page['access_token']);
//
//                $leadForms = json_decode($leadForms->getBody(), true, 512, JSON_THROW_ON_ERROR);
//
//                // Check if there are lead forms and process them
//                if (empty($leadForms['data'])) {
//                    $forms[] = [
//                        'page_name' => $page['name'],
//                        'page_id' => $page['id'],
//                        'error' => 'No active lead forms found for this page.',
//                    ];
//                    continue;
//                }
//
//                foreach ($leadForms as $form) {
//                    $forms[] = [
//                        'name' => $page['name'],
//                        'page_id' => $page['id'],
//                        'form_id' => $form->{LeadgenFormFields::ID},
//                        'form_name' => $form->{LeadgenFormFields::NAME},
//                        'form_status' => $form->{LeadgenFormFields::STATUS},
//                        'created_time' => $form['created_time'],
//                    ];
//                }
//
//            } catch (Exception $e) {
//                $forms[] = ['error' => "Error fetching forms for page: " . $page['name'] . " - " . $e->getMessage()];
//            }
//        }
//
//        dd($forms);
//        return $forms;
//    }
//
//    /**
//     * Fetch all leads from multiple forms
//     */
//    public function getLeadsFromForms(): array
//    {
//        $leadForms = $this->getLeadFormsFromPages();
//        $leadsData = [];
//
//        foreach ($leadForms as $form) {
//            try {
//                $leadgenForm = new LeadgenForm($form['form_id']);
//                $leads = $leadgenForm->getLeads([], [], config('services.facebook.access_token'));
//
//                $leads = json_decode($leads->getLastResponse()->getBody(), true, 512, JSON_THROW_ON_ERROR);
//                foreach ($leads as $lead) {
//                    $leadsData[] = [
//                        'page_name' => $form['page_name'],
//                        'page_id' => $form['page_id'],
//                        'form_name' => $form['form_name'],
//                        'form_id' => $form['form_id'],
//                        'lead_id' => $lead->{LeadFields::ID},
//                        'created_time' => $lead->{LeadFields::CREATED_TIME},
//                        'field_data' => $lead->{LeadFields::FIELD_DATA},
//                    ];
//                }
//            } catch (Exception $e) {
//                $leadsData[] = ['error' => "Error fetching leads for form: " . $form['form_name'] . " - " . $e->getMessage()];
//            }
//        }
//        return $leadsData;
//    }
//
//    /**
//     * Get total lead count for each form
//     */
//    public function getTotalLeadsFromForms(): array
//    {
//        $leadForms = $this->getLeadFormsFromPages();
//        $leadCounts = [];
//
//        foreach ($leadForms as $form) {
//            try {
//                $leadgenForm = new LeadgenForm($form['form_id']);
//                $leads = $leadgenForm->getLeads([], [], config('services.facebook.access_token'));
//
//                $leadCounts[] = [
//                    'page_name' => $form['page_name'],
//                    'page_id' => $form['page_id'],
//                    'form_name' => $form['form_name'],
//                    'form_id' => $form['form_id'],
//                    'total_leads' => count($leads),
//                ];
//            } catch (Exception $e) {
//                $leadCounts[] = ['error' => "Error fetching lead count for form: " . $form['form_name'] . " - " . $e->getMessage()];
//            }
//        }
//        return $leadCounts;
//    }


    /**
     * Upload offline events.
     */
    public function uploadOfflineEvent($eventSourceId, $events)
    {
        return $this->handleErrors(function() use ($eventSourceId, $events) {
            return (new EventSourceGroup($eventSourceId))->createEvent([], $events);
        });
    }

    // ----------------- Batch Requests -----------------

    /**
     * Perform batch API requests.
     */
    public function createBatchRequest($requests)
    {
        return $this->handleErrors(function() use ($requests) {
            return $this->api->callBatch($requests);
        });
    }

    // ----------------- Page Insights -----------------

    /**
     * Get insights for a Facebook page.
     */
    public function getPageInsights($pageId)
    {
        return $this->handleErrors(function() use ($pageId) {
            return (new Page($pageId))->getInsights();
        });
    }

    /**
     * Get details of a Product Catalog.
     */
    public function getProductCatalog($catalogId)
    {
        return $this->handleErrors(function() use ($catalogId) {
            return (new ProductCatalog($catalogId))->read();
        });
    }

    /**
     * Add a product to the Product Catalog.
     */
    public function addProductToCatalog($catalogId, $productData)
    {
        return $this->handleErrors(function() use ($catalogId, $productData) {
            return (new ProductCatalog($catalogId))->createProduct([], $productData);
        });
    }

    /**
     * Get a specific lead form by ID.
     */
    public function getLeadForm($leadFormId)
    {
        return $this->handleErrors(function() use ($leadFormId) {
            return (new LeadgenForm($leadFormId))->read();
        });
    }

    /**
     * Get information about a Business account.
     */
    public function getBusinessInfo($businessId)
    {
        return $this->handleErrors(function() use ($businessId) {
            return (new Business($businessId))->read();
        });
    }

    /**
     * List assets associated with a Business account.
     */
    public function getBusinessAssets($businessId)
    {
        return $this->handleErrors(function() use ($businessId) {
            return (new Business($businessId))->getOwnedAdAccounts();
        });
    }

    /**
     * Get information about the current Ad Account.
     */
    public function getAdAccountInfo()
    {
        return $this->handleErrors(function() {
            return (new AdAccount($this->accountId))->read();
        });
    }

    /**
     * List campaigns under the current Ad Account.
     */
    public function listAdAccountCampaigns()
    {
        return $this->handleErrors(function() {
            $adAccounts = $this->getAdAccounts();
            $campaigns = [];
            foreach ($adAccounts as $adAccount) {
                $adAccountId = $adAccount->{AdAccountFields::ID};
                $campaign = (new AdAccount($adAccountId))->getCampaigns(
                    [
                        CampaignFields::ID,
                        CampaignFields::NAME,
                        CampaignFields::STATUS,
                        CampaignFields::OBJECTIVE,
                        CampaignFields::BUDGET_REMAINING,
                        CampaignFields::SPEND_CAP,
                        CampaignFields::START_TIME,
                        CampaignFields::STOP_TIME,
                    ]
                );
                $campaigns[] = json_decode($campaign->getLastResponse()->getBody(), true, 512, JSON_THROW_ON_ERROR);
            }

            return $campaigns;
        });
    }

    public function getCampaignInsights()
    {
        return $this->handleErrors(function() {
           $adAccounts = $this->getAdAccounts();
           $campaigns = [];
           foreach ($adAccounts as $adAccount) {
               $adAccountId = $adAccount->{AdAccountFields::ID};
               $campaign = (new AdAccount($adAccountId))->getInsights(
                   [
                       AdsInsightsFields::CAMPAIGN_ID,
                       AdsInsightsFields::CAMPAIGN_NAME,
                       AdsInsightsFields::IMPRESSIONS,
                       AdsInsightsFields::CTR,
                       AdsInsightsFields::SPEND,
                       AdsInsightsFields::CONVERSIONS,
                       AdsInsightsFields::REACH,
                       AdsInsightsFields::ACTIONS,
                       AdsInsightsFields::CONVERSION_LEAD_RATE,
                       AdsInsightsFields::CPC,
                       AdsInsightsFields::CPM,
                       AdsInsightsFields::CPP,
                       AdsInsightsFields::WEBSITE_CTR,
                   ]
               );
               $campaigns[] = json_decode($campaign->getLastResponse()->getBody(), true, 512, JSON_THROW_ON_ERROR);
           }

           return $campaigns;
        });
    }

    public function combineCampaignResults()
    {
        return $this->handleErrors(function () {
            // Fetch Campaign Details
            $campaignDetails = $this->listAdAccountCampaigns();

            // If Campaign Details are empty, exit early
            if (empty($campaignDetails)) {
                Log::warning("No campaign details found for Ad Account: {$this->accountId}. Unable to process any data.");
                return []; // Nothing to process since insights depend on campaign details
            }

            // Fetch Campaign Insights
            $campaignInsights = $this->getCampaignInsights();

            // Initialize the merged campaigns array
            $mergedCampaigns = [];

            // Iterate through each campaign detail and combine it with insights
            foreach ($campaignDetails as $campaignDetailArray) {
                foreach ($campaignDetailArray['data'] as $campaignDetail) {
                    $campaignId = $campaignDetail[CampaignFields::ID] ?? null;

                    // Skip invalid campaign entries with missing IDs
                    if (empty($campaignId)) {
                        Log::warning("Campaign detail with missing ID skipped.");
                        continue;
                    }

                    // Find matching insights for the campaign
                    $matchingInsight = !empty($campaignInsights[0]['data'])
                        ? array_filter($campaignInsights[0]['data'], function ($insight) use ($campaignId) {
                            return $insight[AdsInsightsFields::CAMPAIGN_ID] === $campaignId;
                        })
                        : []; // If insights are empty, this will stay an empty array

                    // Either merge with insights or add the detail with no insights
                    if (!empty($matchingInsight)) {
                        $insight = array_values($matchingInsight)[0]; // Take the first match
                        $mergedCampaigns[] = array_merge($campaignDetail, $insight);
                    } else {
                        // Add the campaign detail with no insights
                        $campaignDetail['insights'] = null; // No insights found for this campaign
                        $mergedCampaigns[] = $campaignDetail;
                    }
                }
            }

            // Log if no campaigns were merged
            if (empty($mergedCampaigns)) {
                Log::info("No campaigns were merged. All campaign details had no matching insights.");
            }

            // Return the merged result (or just the details if no insights exist)
            return $mergedCampaigns;
        });
    }


    private function getAccessTokenFromDb()
    {
        return $this->handleErrors(function (){
           $record = SocialMediaTokens::where('platform', 'facebook')->first();
           return [
               'access_token' => $record->access_token,
               'expires_at' => $record->expires_at,
           ];
        });
    }

    private function isAccessTokenExpired($expirationTime): bool
    {
        return now()->greaterThan($expirationTime);
    }


}
