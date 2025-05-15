<?php

namespace Workdo\MarketingPlan\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use AllowDynamicProperties;
use FacebookAds\Api;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Workdo\MarketingPlan\Services\FacebookAdsService;
use Workdo\MarketingPlan\Services\GoogleAdsService;
use function Laravel\Prompts\pause;

class MarketingIntegrationController extends Controller
{
    private $api;
    private string $customerId = "834-438-7464";//google

    private string $accountId; //facebook
    protected FacebookAdsService $facebookAdsService;
    protected GoogleAdsService $googleAdsService;

    public function __construct($accountId = null)
    {
        $this->facebookAdsService = new FacebookAdsService($accountId);
        $this->googleAdsService = new GoogleAdsService($this->customerId);

    }

    public function getFacebookAdsMetrics(): array
    {
        $facebookCampaigns = $this->facebookAdsService->combineCampaignResults();
        $facebookInsights = $this->facebookAdsService->getAllCampaignInsights();

        // Transform campaigns to ensure fields are consistently structured
        $facebookCampaigns = array_map(static function ($campaign) {
            return [
                'campaign_name' => $campaign['name'] ?? 'N/A', // Map 'name' to 'campaign_name'
                'status' => $campaign['status'] ?? 'N/A',
                'objective' => $campaign['objective'] ?? 'N/A',
                'budget' => $campaign['insights']['spend'] ?? $campaign['budget_remaining'] ?? '0',
                'spend' => $campaign['insights']['spend'] ?? 'N/A',
                'impressions' => $campaign['insights']['impressions'] ?? 'N/A',
                'clicks' => $campaign['insights']['clicks'] ?? 'N/A',
                'ctr' => $campaign['insights']['ctr'] ?? 'N/A',
                'conversions' => $campaign['insights']['conversions'] ?? 'N/A',
                'budget_remaining' => $campaign['budget_remaining'] ?? '0',
                'start_time' => $campaign['start_time'] ?? 'N/A',
                'stop_time' => $campaign['stop_time'] ?? 'N/A',
            ];
        }, $facebookCampaigns);

        return [
            'spend' => $facebookInsights['totals']['spend'],
            'impressions' => $facebookInsights['totals']['impressions'],
            'clicks' => $facebookInsights['totals']['clicks'],
            'reach' => $facebookInsights['totals']['reach'],
            'ctr' => $facebookInsights['totals']['ctr'],
            'budget' => $facebookInsights['totals']['budget'],
            'budget_remaining' => $facebookInsights['totals']['budget_remaining'],
            'conversions' => $facebookInsights['conversions'],
            'campaigns' => $facebookCampaigns, // Mapped campaigns with proper fields

            // Performance insights
            'adPerformanceByAge' => $facebookInsights['agePerformance'],
            'adPerformanceByLocation' => $facebookInsights['locationPerformance'],
            'adPerformanceByDevice' => $facebookInsights['devicePerformance'],
            'topAds' => $facebookInsights['topAds'],

            // Lead generation insights
            'totalLeads' => $this->facebookAdsService->getTotalLeads(),
            'allLeads' => $this->facebookAdsService->getLeadsFromAds(),
            // Uncomment if needed:
            // 'leadConversionRate' => $facebookInsights['lead_conversion_rate'],
            // 'topLeadGenerationCampaigns' => $facebookInsights['top_lead_campaigns'],
        ];
    }

    public function getGoogleAdsMetrics(): array
    {
        $googleCampaigns = $this->googleAdsService->getActiveCampaignsWithInsights();
        $googleInsights = $this->googleAdsService->getTotalMetrics();

        return [
            'spend' => $googleInsights['campaign']['spend'] ?? 0,
            'impressions' => $googleInsights['impressions'] ?? 0,
            'clicks' => $googleInsights['clicks'] ?? 0,
            'reach' => $googleInsights['reach'] ?? 0,
            'ctr' => $googleInsights['ctr'] ?? 0,
            'budget' => $googleInsights['budget'] ?? 0,
            'budget_remaining' => $googleInsights['budget_remaining'] ?? 0,
            'conversions' => $googleInsights['conversions'] ?? 0,
            'campaigns' => $googleCampaigns['campaigns'] ?? [], // Ensure we return all active campaigns

            // Placeholder for performance insights from Google Ads (if available)
            'adPerformanceByAge' => $googleCampaigns['agePerformance'] ?? [],
            'adPerformanceByLocation' => $googleCampaigns['locationPerformance'] ?? [],
            'adPerformanceByDevice' => $googleCampaigns['devicePerformance'] ?? [],
//            'topAds' => $googleCampaigns['top_ads'] ?? [],

            // Google Ads lead generation (if applicable)
            'totalLeads' => $googleCampaigns['total_leads'] ?? 0,
//            'leadConversionRate' => $googleCampaigns['lead_conversion_rate'] ?? 0,
//            'topLeadGenerationCampaigns' => $googleCampaigns['top_lead_campaigns'] ?? [],
        ];
    }

    public function index()
    {
//        xdebug_start_trace('/tmp/execution_trace');

        if(Auth::user()->isAbleTo('marketing plan manage')){
            $facebookMetrics = $this->getFacebookAdsMetrics();
            $googleMetrics = $this->getGoogleAdsMetrics();

            // Fetch top-level metrics
            $totalAdSpend = $facebookMetrics['spend'] + $googleMetrics['spend'];
            $totalImpressions = $facebookMetrics['impressions'] + $googleMetrics['impressions'];
            $totalClicks = $facebookMetrics['clicks'] + $googleMetrics['clicks'];
            $totalReach = $facebookMetrics['reach'] + $googleMetrics['reach'];
            $totalCtr = $facebookMetrics['ctr'] + $googleMetrics['ctr'];
            $totalBudget = $facebookMetrics['budget'] + $googleMetrics['budget'];
            $totalBudgetRemaining = $facebookMetrics['budget_remaining'] + $googleMetrics['budget_remaining'];
            $totalConversions = $facebookMetrics['conversions'] + $googleMetrics['conversions'];
            $activeCampaignsCount = count($facebookMetrics['campaigns']) + count($googleMetrics['campaigns']);
            $avgCPC = $totalAdSpend / max($totalClicks, 1);

            // Fetch campaign performance data
            $facebookCampaigns = $this->facebookAdsService->listAdAccountCampaigns();
            $googleCampaigns = $this->googleAdsService->getActiveCampaignsWithInsights();
            $campaigns = array_merge($facebookCampaigns, $googleCampaigns);

            // Fetch Ad Sets & Targeting Insights
            $fbAdPerformance = [
                'agePerformance' => $facebookMetrics['adPerformanceByAge'],
                'locationPerformance' => $facebookMetrics['adPerformanceByLocation'],
                'devicePerformance' => $facebookMetrics['adPerformanceByDevice'],
            ];
            $googleAdPerformance = [
                'agePerformance' => $facebookMetrics['adPerformanceByAge'],
                'locationPerformance' => $facebookMetrics['adPerformanceByLocation'],
                'devicePerformance' => $facebookMetrics['adPerformanceByDevice'],
            ];

            // Fetch Top Performing Ads
            $topAds = $this->facebookAdsService->getCampaignInsights()['top_ads'];

            // Fetch Lead Generation Performance
            $totalLeads = $this->facebookAdsService->getLeadForms()['total_leads'];
            $leadFormConversionRate = $this->facebookAdsService->getCampaignInsights()['lead_conversion_rate'];
            $topLeadGenerationCampaigns = $this->facebookAdsService->getCampaignInsights()['top_lead_campaigns'];
//            xdebug_stop_trace();

            return view('marketing-plan::social.reports', compact(
                'totalAdSpend', 'totalImpressions', 'totalClicks', 'totalConversions', 'activeCampaignsCount', 'avgCPC',
                'campaigns', 'fbAdPerformance','googleAdPerformance', 'topAds',
                'totalLeads', 'leadFormConversionRate', 'topLeadGenerationCampaigns', 'totalReach', 'totalCtr', 'totalBudget',
                'totalBudgetRemaining','totalBudget', 'facebookCampaigns', 'googleCampaigns'
            ));
        }


        return redirect()->back()->with('error', __('Permission denied'));

    }

    public function facebook()
    {
        if(Auth::user()->isAbleTo('marketing plan manage')) {
            $data = $this->getFacebookAdsMetrics();

            return view('marketing-plan::social.facebook', compact('data'));
        }

        return redirect()->back()->with('error', __('Permission denied'));
    }

    public function google()
    {
        if(Auth::user()->isAbleTo('marketing plan manage')) {
            $data = $this->getGoogleAdsMetrics();

            return view('marketing-plan::social.google', compact('data'));
        }

        return redirect()->back()->with('error', __('Permission denied'));
    }


    public function checkActivePlatform()
    {
        if (Auth::user()->isAbleTo('marketing plan manage')){
            //$marketingPlatform = MarketingPlatform::where('user', Auth::user())->
        }
    }

    // Create Facebook Campaign
    public function createFacebookCampaign(Request $request)
    {
        $data = $request->all();
        $response = $this->facebookAdsService->createCampaign($data);
        return redirect()->back()->with('message', 'Facebook Campaign Created Successfully!');
    }

    public function updateFacebookCampaign(Request $request)
    {
        $validated = $request->validate([
            'campaign_id' => 'required',
            'labels' => 'required|array',
        ]);
        $response = $this->facebookAdsService->updateCampaign($validated['campaign_id'], $validated['labels'], $request['spendCap']);
        return redirect()->back()->with('message', 'Facebook Campaign Updated Successfully!');
    }

    public function createGoogleCampaign(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'daily_budget' => 'required|numeric',
            'status' => 'required',
        ]);
        $campaign = $this->googleAdsService->createCampaign($validated['name'], $validated['daily_budget'], $validated['status']);
        return redirect()->back()->with('message', 'Google Campaign Created Successfully!');
    }

    public function fetchFacebookCampaigns()
    {
        $campaigns = $this->facebookAdsService->listAdAccountCampaigns();
        return view('marketing-plan::social.facebook_campaigns', compact('campaigns'));
    }

    public function getGoogleCampaignReportById(Request $request)
    {
        $campaignId = $request->input('campaign_id', 'default_campaign_id');
        $dateRange = $request->input('dateRange', 'default_date_range');

        $report = $this->googleAdsService->getCampaignReport($campaignId, $dateRange);

        return view('marketing-plan::social.google_campaign_report', compact('report'));
    }

    public function getAllGoogleCampaignReports()
    {
        $reports = $this->googleAdsService->getActiveCampaignsWithInsights();
        return view('marketing-plan::social.google_reports', compact('reports'));
    }

    public function scheduleGoogleAd(Request $request)
    {
        $validated = $request->validate([
            'campaign_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);
        $this->googleAdsService->scheduleCampaign($validated['campaign_id'], $validated['start_date'], $validated['end_date']);
        return redirect()->back()->with('message', 'Google Ads Scheduled Successfully!');
    }

    public function createFacebookAdSet(Request $request)
    {
        $validated = $request->validate([
            'campaign_id' => 'required',
            'name' => 'required',
            'daily_budget' => 'required|numeric',
            'targeting' => 'required',
            'budget_type' => 'required'
        ]);
        $this->facebookAdsService->createAdSet($validated['campaign_id'], $validated['name'], $validated['daily_budget'] ,$validated['targeting'], $validated['budget_type']);
        return redirect()->back()->with('message', 'Facebook Ad Sets Created Successfully!');
    }

    public function updateFacebookAdSetBudget(Request $request)
    {
        $validated = $request->validate([
            'adSetId' => 'required',
            'newBudget' => 'required',
            'budget_type' => 'required'
        ]);
        $response = $this->facebookAdsService->updateAdSetBudget($validated['adSetId'], $validated['newBudget'] ,$validated['budget_type']);
        return redirect()->back()->with('message', 'Facebook Ad Set Budget Updated Successfully!');
    }

    public function scheduleFacebookAd(Request $request)
    {
        $validated = $request->validate([
            'adId' => 'required',
            'startTime' => 'required',
            'endTime' => 'required',
        ]);
        $this->facebookAdsService->scheduleAd($validated['adId'], $validated['startTime'], $validated['endTime']);
        return redirect()->back()->with('message', 'Facebook Ad Scheduled Successfully!');
    }

    public function createFacebookCustomAudience(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $this->facebookAdsService->createCustomAudience($validated['name'], $validated['description']);
        return redirect()->back()->with('message', 'Facebook Custom Audience Created Successfully!');
    }

    public function getGoogleKeywordIdeas(Request $request)
    {
        $validated = $request->validate([
            'seedKeyword' => 'required',
        ]);
        return $this->googleAdsService->getKeywordIdeas($validated['seedKeyword']);
    }

    public function createGoogleAsset(Request $request)
    {
        $validated = $request->validate([
            'assetType' => 'required',
            'assetData' => 'required',
        ]);
        $this->googleAdsService->createAsset($validated['assetType'], $validated['assetData']);
        return redirect()->back()->with('message', 'Google Asset Created Successfully!');
    }

    public function fetchFacebookLeadForms()
    {
        $leadForms = $this->facebookAdsService->getLeadForms();
        return view('marketing-plan::social.lead_forms', compact('leadForms'));
    }

    public function fetchInstagramInsights()
    {
        $insights = $this->facebookAdsService->getInstagramInsights();
        return view('marketing-plan::social.instagram_insights', compact('insights'));
    }

    public function fetchFacebookProductCatalog()
    {
        $catalog = $this->facebookAdsService->getFacebookProductCatalog();
        return view('marketing-plan::social.facebook_product_catalog', compact('catalog'));
    }

    public function getAccountId(): string
    {
        return $this->accountId;
    }

    public function setAccountId(string $accountId = null): MarketingIntegrationController
    {
        $this->accountId = $accountId ?? 'act_' . config('services.facebook.account_id');
        return $this;
    }

}
