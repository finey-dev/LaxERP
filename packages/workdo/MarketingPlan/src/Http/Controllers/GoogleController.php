<?php

namespace Workdo\MarketingPlan\Http\Controllers;

use Workdo\MarketingPlan\Http\Controllers\MarketingIntegrationController;
use Workdo\MarketingPlan\Services\FacebookAdsService;

class GoogleController extends MarketingIntegrationController
{

    public function __construct($facebookAdsService){
        parent::__construct($facebookAdsService);
    }
    public function index()
    {
        return view('marketingplan::index');
    }
}
