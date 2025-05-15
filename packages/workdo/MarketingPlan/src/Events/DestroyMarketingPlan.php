<?php

namespace Workdo\MarketingPlan\Events;

use Illuminate\Queue\SerializesModels;

class DestroyMarketingPlan
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $MarketingPlans;

    public function __construct($MarketingPlans)
    {
        $this->MarketingPlans = $MarketingPlans;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
