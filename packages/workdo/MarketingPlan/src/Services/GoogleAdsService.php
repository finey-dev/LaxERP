<?php

namespace Workdo\MarketingPlan\Services;

use AllowDynamicProperties;
use Google\Ads\GoogleAds\Lib\V18\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\Util\V18\ResourceNames;
use Google\Ads\GoogleAds\V18\Common\ImageAsset;
use Google\Ads\GoogleAds\V18\Common\KeywordInfo;
use Google\Ads\GoogleAds\V18\Common\ManualCpc;
use Google\Ads\GoogleAds\V18\Common\TargetCpa;
use Google\Ads\GoogleAds\V18\Common\YoutubeVideoAsset;
use Google\Ads\GoogleAds\V18\Enums\AdGroupAdStatusEnum\AdGroupAdStatus;
use Google\Ads\GoogleAds\V18\Enums\AdvertisingChannelTypeEnum\AdvertisingChannelType;
use Google\Ads\GoogleAds\V18\Enums\BiddingStrategyTypeEnum\BiddingStrategyType;
use Google\Ads\GoogleAds\V18\Enums\CampaignStatusEnum;
use Google\Ads\GoogleAds\V18\Enums\CampaignStatusEnum\CampaignStatus;
use Google\Ads\GoogleAds\V18\Enums\ConversionActionCategoryEnum\ConversionActionCategory;
use Google\Ads\GoogleAds\V18\Enums\KeywordMatchTypeEnum\KeywordMatchType;
use Google\Ads\GoogleAds\V18\Resources\AdGroupAd;
use Google\Ads\GoogleAds\V18\Resources\AdGroupCriterion;
use Google\Ads\GoogleAds\V18\Resources\Asset;
use Google\Ads\GoogleAds\V18\Resources\Campaign;
use Google\Ads\GoogleAds\V18\Resources\CampaignBudget;
use Google\Ads\GoogleAds\V18\Resources\ConversionAction;
use Google\Ads\GoogleAds\V18\Resources\SmartCampaignSetting;
use Google\Ads\GoogleAds\V18\Services\AdGroupAdOperation;
use Google\Ads\GoogleAds\V18\Services\AdGroupCriterionOperation;
use Google\Ads\GoogleAds\V18\Services\AssetOperation;
use Google\Ads\GoogleAds\V18\Services\CampaignBudgetOperation;
use Google\Ads\GoogleAds\V18\Services\CampaignOperation;
use Google\Ads\GoogleAds\V18\Services\ConversionActionOperation;
use Google\Ads\GoogleAds\V18\Services\SearchGoogleAdsRequest;
use Google\Auth\Credentials\UserRefreshCredentials;
use Google\Service\Dfareporting\RemarketingList;
use Illuminate\Support\Facades\Log;

#[AllowDynamicProperties] class GoogleAdsService
{
    protected $client;
    protected $customerId;



    public function __construct($customerId)
    {
//        $this->googleAdsClient = (new GoogleAdsClientBuilder())
//            ->fromFile(config_path('google_ads_php.ini'))
//            ->build();
        $scopes = ['https://www.googleapis.com/auth/adwords']; // Required scope

        $this->googleAdsClient = (new GoogleAdsClientBuilder())
            ->withDeveloperToken("J7tFJA_yHs5gZcGcg_mw-A")
            ->withOAuth2Credential(new UserRefreshCredentials(
                $scopes,
                [
                    'client_id' => "950410331433-r9mhmnr1pj1etibriiiotdcsaiupfksj.apps.googleusercontent.com",
                    'client_secret' => "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",
                    'refresh_token' => "1//09_zuE3WE56Z6CgYIARAAGAkSNgF-L9IrZYiDjdLOFFFSJVL8zFUwV5HNfwbjOJq2DF8VAQOmw3R0AL_EjvyFJX-kQq2tQ4MC_A",
                ]
            ))
            ->build();
        $this->customerId = $customerId;
    }

    // ----------------- Campaign Management -----------------

    /**
     * Create a new Search Campaign with a specified status.
     */
    public function createCampaign($name, $dailyBudget, $status = 'PAUSED')
    {
        try {
            $statusEnum = match (strtoupper($status)) {
                'ENABLED' => CampaignStatus::ENABLED,
                'REMOVED' => CampaignStatus::REMOVED,
                default => CampaignStatus::PAUSED,
            };

            // Create a new campaign budget
            $budgetResourceName = $this->createCampaignBudget($dailyBudget);

            // Define the campaign
            $campaign = new Campaign([
                'name' => $name,
                'advertising_channel_type' => AdvertisingChannelType::SEARCH,
                'status' => $statusEnum, // Allow dynamic status
                'campaign_budget' => $budgetResourceName,
                'manual_cpc' => new ManualCpc(),
            ]);

            // Create a campaign operation
            $campaignOperation = new CampaignOperation();
            $campaignOperation->setCreate($campaign);

            // Execute the request
            $campaignServiceClient = $this->googleAdsClient->getCampaignServiceClient();
            $response = $campaignServiceClient->mutateCampaigns($this->customerId, [$campaignOperation]);

            return $response->getResults()[0];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }


    /*
     * Date Range Keywords(TODAY, YESTERDAY, LAST_7_DAYS, LAST_30_DAYS, LAST_90_DAYS, THIS_WEEK_MON_TODAY
     * LAST_WEEK, LAST_MONTH, THIS_MONTH, ALL_TIME)
     */
    public function getCampaignReport($campaignId, $dateRange = 'LAST_30_DAYS')
    {
        try {
            $googleAdsServiceClient = $this->googleAdsClient->getGoogleAdsServiceClient();

            // Check if it's a custom date range or predefined
            if (preg_match('/^\d{8}$/', $dateRange)) {
                // Custom date format (YYYYMMDD)
                $query = "SELECT campaign.id, campaign.name, metrics.impressions, metrics.clicks, metrics.cost_micros, metrics.conversions
                      FROM campaign
                      WHERE campaign.id = @campaignId
                      AND segments.date BETWEEN '$dateRange' AND '$dateRange'";
            } else {
                // Predefined date range
                $query = "SELECT campaign.id, campaign.name, metrics.impressions, metrics.clicks, metrics.cost_micros, metrics.conversions
                      FROM campaign
                      WHERE campaign.id = @campaignId
                      AND segments.date DURING $dateRange";
            }

            $response = $googleAdsServiceClient->search($this->customerId, $query, ['campaignId' => $campaignId]);

            return $this->getInsightsFromResponse($response);

        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Schedule a campaign by setting start and end times.
     */
    public function scheduleCampaign($campaignId, $startDate, $endDate)
    {
        try {
            $campaign = new Campaign([
                'resource_name' => $campaignId,
                'start_date' => date('Ymd', strtotime($startDate)),
                'end_date' => date('Ymd', strtotime($endDate)),
            ]);

            $campaignOperation = new CampaignOperation();
            $campaignOperation->setUpdate($campaign);
            $campaignOperation->setUpdateMask(['start_date', 'end_date']);

            $campaignService = $this->googleAdsClient->getCampaignServiceClient();
            $response = $campaignService->mutateCampaigns($this->customerId, [$campaignOperation]);

            return $response->getResults()[0];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get keyword ideas based on a seed keyword.
     */
    public function getKeywordIdeas($seedKeyword)
    {
        try {
            $keywordPlanIdeaService = $this->googleAdsClient->getKeywordPlanIdeaServiceClient();

            $query = "SELECT keyword_plan_idea.text, keyword_plan_idea.avg_monthly_searches FROM keyword_plan_idea WHERE keyword_plan_idea.text LIKE '%$seedKeyword%'";

            $response = $keywordPlanIdeaService->search($this->customerId, $query);

            $keywordIdeas = [];
            foreach ($response->iterateAllElements() as $row) {
                $keywordIdeas[] = [
                    'keyword' => $row->getKeywordPlanIdea()->getText(),
                    'average_searches' => $row->getKeywordPlanIdea()->getAvgMonthlySearches(),
                ];
            }

            return $keywordIdeas;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Add negative keywords to an ad group using ResourceNames.
     */
    public function addNegativeKeyword($customerId, $adGroupId, $keywordText, $matchType = 'exact')
    {
        try {
            $adGroupCriterionServiceClient = $this->googleAdsClient->getAdGroupCriterionServiceClient();

            $adGroupCriterionOperation = new AdGroupCriterionOperation([
                'create' => new AdGroupCriterion([
                    'ad_group' => ResourceNames::forAdGroup($customerId, $adGroupId),
                    'keyword' => new KeywordInfo([
                        'text' => $keywordText,
                        'match_type' => $this->getKeywordMatchType($matchType),
                    ]),
                    'negative' => true,
                ]),
            ]);

            $response = $adGroupCriterionServiceClient->mutateAdGroupCriteria($customerId, [$adGroupCriterionOperation]);

            return $response->getResults()[0];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function createConversionAction($customerId, $name)
    {
        $conversionActionServiceClient = $this->client->getConversionActionServiceClient();
        $conversionActionOperation = new ConversionActionOperation([
            'create' => new ConversionAction([
                'name' => $name,
                'category' => ConversionActionCategory::LEAD,
            ]),
        ]);
        return $conversionActionServiceClient->mutateConversionActions($customerId, [$conversionActionOperation]);
    }

    public function createSmartCampaign($customerId, $businessName, $phoneNumber)
    {
        $campaignServiceClient = $this->client->getCampaignServiceClient();
        $campaignOperation = new CampaignOperation([
            'create' => new Campaign([
                'name' => 'Smart Campaign',
                'advertising_channel_type' => AdvertisingChannelType::SMART,
                'smart_campaign_setting' => new SmartCampaignSetting([
                    'business_name' => $businessName,
                    'phone_number' => $phoneNumber,
                ]),
            ]),
        ]);
        return $campaignServiceClient->mutateCampaigns($customerId, [$campaignOperation]);
    }

    public function createRemarketingList($customerId, $name)
    {
        $remarketingListServiceClient = $this->client->getRemarketingListServiceClient();
        $remarketingListOperation = new RemarketingListOperation([
            'create' => new RemarketingList([
                'name' => $name,
                'membership_life_span' => 30,
            ]),
        ]);
        return $remarketingListServiceClient->mutateRemarketingLists($customerId, [$remarketingListOperation]);
    }

    /**
     * Get the keyword match type enum from a string.
     */
    private function getKeywordMatchType($matchType)
    {
        return match (strtolower($matchType)) {
            'broad' => KeywordMatchType::BROAD,
            'phrase' => KeywordMatchType::PHRASE,
            default => KeywordMatchType::EXACT,
        };
    }

    /**
     * Add, update, or remove keywords from an ad group.
     */
    public function manageKeywords($adGroupId, $keywordText, $matchType = 'exact', $action = 'add')
    {
        try {
            $keywordInfo = new KeywordInfo([
                'text' => $keywordText,
                'match_type' => $this->getKeywordMatchType($matchType),
            ]);

            $adGroupCriterion = new AdGroupCriterion([
                'ad_group' => $adGroupId,
                'status' => \Google\Ads\GoogleAds\V18\Enums\AdGroupCriterionStatusEnum\AdGroupCriterionStatus::ENABLED,
                'keyword' => $keywordInfo,
            ]);

            $adGroupCriterionOperation = new AdGroupCriterionOperation();

            if ($action === 'add') {
                $adGroupCriterionOperation->setCreate($adGroupCriterion);
            } elseif ($action === 'update') {
                $adGroupCriterionOperation->setUpdate($adGroupCriterion);
                $adGroupCriterionOperation->setUpdateMask(['keyword']);
            } elseif ($action === 'remove') {
                $adGroupCriterionOperation->setRemove($adGroupId);
            } else {
                throw new \Exception('Invalid action specified. Use add, update, or remove.');
            }

            $adGroupCriterionService = $this->googleAdsClient->getAdGroupCriterionServiceClient();
            $response = $adGroupCriterionService->mutateAdGroupCriteria($this->customerId, [$adGroupCriterionOperation]);

            return $response->getResults()[0];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Create a new YouTube Video Ad.
     */
    public function createVideoAd($adGroupId, $youtubeVideoId, $finalUrl)
    {
        try {
            $videoAsset = new YoutubeVideoAsset([
                'youtube_video_id' => $youtubeVideoId
            ]);

            $adGroupAd = new AdGroupAd([
                'ad_group' => ResourceNames::forAdGroup($this->customerId, $adGroupId),
                'status' => AdGroupAdStatus::PAUSED,
                'ad' => [
                    'video_ad' => $videoAsset,
                    'final_urls' => [$finalUrl],
                ],
            ]);

            $adGroupAdOperation = new AdGroupAdOperation();
            $adGroupAdOperation->setCreate($adGroupAd);

            $adGroupAdService = $this->googleAdsClient->getAdGroupAdServiceClient();
            $response = $adGroupAdService->mutateAdGroupAds($this->customerId, [$adGroupAdOperation]);

            return $response->getResults()[0];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Create a new asset (image or video).
     */
    public function createAsset($assetType, $assetData)
    {
        try {
            $asset = new Asset();
            if ($assetType === 'image') {
                $asset->setImageAsset(new ImageAsset([
                    'data' => base64_encode(file_get_contents($assetData))
                ]));
            } elseif ($assetType === 'video') {
                $asset->setYoutubeVideoAsset(new YoutubeVideoAsset([
                    'youtube_video_id' => $assetData
                ]));
            }

            $assetOperation = new AssetOperation();
            $assetOperation->setCreate($asset);

            $assetService = $this->googleAdsClient->getAssetServiceClient();
            $response = $assetService->mutateAssets($this->customerId, [$assetOperation]);

            return $response->getResults()[0];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Create a campaign budget.
     */
    public function createCampaignBudget($amount)
    {
        try {
            $budget = new CampaignBudget([
                'name' => 'Budget ' . uniqid('', true),
                'amount_micros' => $amount * 1000000,
                'delivery_method' => \Google\Ads\GoogleAds\V18\Enums\BudgetDeliveryMethodEnum\BudgetDeliveryMethod::STANDARD,
            ]);

            $budgetOperation = new CampaignBudgetOperation();
            $budgetOperation->setCreate($budget);

            $budgetService = $this->googleAdsClient->getCampaignBudgetServiceClient();
            $response = $budgetService->mutateCampaignBudgets($this->customerId, [$budgetOperation]);

            return $response->getResults()[0];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Set Target CPA Bidding Strategy.
     */
    public function setTargetCpaBidding($campaignId, $targetCpa)
    {
        try {
            $campaign = new Campaign([
                'resource_name' => ResourceNames::forCampaign($this->customerId, $campaignId),
                'bidding_strategy_type' => BiddingStrategyType::TARGET_CPA,
                'target_cpa' => new TargetCpa([
                    'target_cpa_micros' => $targetCpa * 1000000,
                ]),
            ]);

            $campaignOperation = new CampaignOperation();
            $campaignOperation->setUpdate($campaign);
            $campaignOperation->setUpdateMask(['bidding_strategy_type', 'target_cpa']);

            $campaignService = $this->googleAdsClient->getCampaignServiceClient();
            $response = $campaignService->mutateCampaigns($this->customerId, [$campaignOperation]);

            return $response->getResults()[0];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get all active campaigns and their reports/insights.
     */
    public function getActiveCampaignsWithInsights($startDate = null, $endDate = null, $datePreset = 'LAST_30_DAYS', $campaignId = null): array
    {
        try {
            $googleAdsServiceClient = $this->googleAdsClient->getGoogleAdsServiceClient();

            $dateCondition = $startDate && $endDate
                ? "segments.date BETWEEN '{$startDate}' AND '{$endDate}'"
                : "segments.date DURING {$datePreset}";

            $campaignCondition = $campaignId
                ? "AND campaign.id = {$campaignId}"
                : "";

            $query = "
                        SELECT
                            campaign.id,
                            campaign.name,
                            campaign_budget.amount_micros,
                            ad_group.id,
                            ad_group.name,
                            SUM(metrics.impressions) AS impressions,
                            SUM(metrics.clicks) AS clicks,
                            SUM(metrics.cost_micros) AS cost,
                            SUM(metrics.conversions) AS conversions,
                            SAFE_DIVIDE(SUM(metrics.clicks), SUM(metrics.impressions)) * 100 AS ctr
                        FROM ad_group
                        JOIN campaign ON ad_group.campaign = campaign.resource_name
                        JOIN campaign_budget ON campaign.campaign_budget = campaign_budget.resource_name
                        WHERE {$dateCondition} {$campaignCondition}
                        GROUP BY campaign.id, campaign.name, campaign_budget.amount_micros, ad_group.id, ad_group.name
                        ORDER BY impressions DESC
                    ";


            $searchRequest = new SearchGoogleAdsRequest([
                'customer_id' => $this->customerId,
                'query' => $query,
            ]);

            $response = $googleAdsServiceClient->search($searchRequest);

            $agePerformance = $this->fetchBreakdown('segments.age_range', $dateCondition, $campaignCondition);
            $locationPerformance = $this->fetchBreakdown('geo.target_country', $dateCondition, $campaignCondition);
            $devicePerformance = $this->fetchBreakdown('segments.device', $dateCondition, $campaignCondition);

            return $insights = [
                'campaign' => $this->getInsightsFromResponse($response),
                'agePerformance' => $agePerformance,
                'locationPerformance' => $locationPerformance,
                'devicePerformance' => $devicePerformance,
            ];


        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getTotalMetrics(): ?array
    {
        try {
            $insights = $this->getActiveCampaignsWithInsights()['campaign'];
            $totals = [
                'impressions' => 0,
                'clicks' => 0,
                'cost' => 0,
                'conversions' => 0,
                'spend' => 0,
                'ctr' => 0,
                'budget' => 0,
                'reach' => 0,
                'budget_remaining' => 0,
            ];

            foreach ($insights as $campaign) {
                $totals['impressions'] += $campaign['impressions'];
                $totals['clicks'] += $campaign['clicks'];
                $totals['cost'] += $campaign['cost'];
                $totals['conversions'] += $campaign['conversions'];
                $totals['spend'] += $campaign['spend'];
                $totals['budget'] += $campaign['budget'];
                $totals['reach'] += $campaign['reach'];
                $totals['budget_remaining'] += $campaign['budget_remaining'];

                // Summing `ctr` separately since it's averaged later
                if (isset($campaign['ctr']) && $campaign['impressions'] > 0) {
                    // Weighted CTR based on impressions
                    $totals['ctr'] += $campaign['ctr'] * $campaign['impressions'];
                }
            }

            // Calculate the average CTR across all campaigns
            $totalImpressions = array_sum(array_column($insights, 'impressions'));
            $totals['ctr'] = $totalImpressions > 0 ? $totals['ctr'] / $totalImpressions : 0;

            return $totals;
        }catch (\Exception $e) {
            Log::debug('error', [$e->getMessage()]) ;
            return null;
        }

    }


    private function getInsightsFromResponse($response): array
    {
        $campaigns = [];
        foreach ($response->iterateAllElements() as $row) {
            $budget_micros = $row->getCampaignBudget()->getAmountMicros();
            $cost_micros = $row->getMetrics()->getCostMicros();
            $budget_remaining = ($budget_micros - $cost_micros) / 1_000_000;

            $campaigns[] = [
                'campaign_id' => $row->getCampaign()->getId(),
                'campaign_name' => $row->getCampaign()->getName(),
                'impressions' => $row->getMetrics()->getImpressions(),
                'clicks' => $row->getMetrics()->getClicks(),
                'cost' => $row->getMetrics()->getCostMicros() / 1000000, // Convert micros to currency
                'conversions' => $row->getMetrics()->getConversions(),
                'spend' => $row->getMetrics()->getSpend(),
                'ctr' => $row->getMetrics()->getCtr(),
                'budget' => $row->getMetrics()->getBudget(),
                'reach' => $row->getMetrics()->getReach(),
                'budget_remaining' => $budget_remaining,
            ];
        }

        return  $campaigns;
    }

    private function fetchBreakdown(string $breakdown, string $dateCondition, string $campaignCondition)
    {
        $googleAdsServiceClient = $this->googleAdsClient->getGoogleAdsServiceClient();
        $query = "
            SELECT
                {$breakdown},
                metrics.impressions,
                metrics.clicks,
                metrics.cost_micros,
                metrics.conversions
            FROM ad_group
            WHERE {$dateCondition} {$campaignCondition}
            ORDER BY metrics.impressions DESC
        ";

        $searchRequest = new SearchGoogleAdsRequest([
            'customer_id' => $this->customerId,
            'query' => $query,
        ]);

        $response = $googleAdsServiceClient->search($searchRequest);

        $data = [];

        foreach ($response as $row) {
            $data[] = [
                'category' => $row->getField($breakdown)->getValue() ?? 'Unknown',
                'impressions' => $row->getMetrics()->getImpressions(),
                'clicks' => $row->getMetrics()->getClicks(),
                'spend' => $row->getMetrics()->getCostMicros() / 1_000_000,
                'conversions' => $row->getMetrics()->getConversions(),
            ];
        }

        return $data;
    }


}
