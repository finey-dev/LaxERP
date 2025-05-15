<?php

namespace Workdo\MarketingPlan\Events;

use Illuminate\Queue\SerializesModels;

class UpdateMarketingPlan
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $MarketingPlan;

    public function __construct($request, $MarketingPlan)
    {
        $this->request = $request;
        $this->MarketingPlan = $MarketingPlan;
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
