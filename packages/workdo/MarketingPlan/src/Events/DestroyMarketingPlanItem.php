<?php

namespace Workdo\MarketingPlan\Events;

use Illuminate\Queue\SerializesModels;

class DestroyMarketingPlanItem
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $marketingplanitem;

    public function __construct($marketingplanitem)
    {
        $this->marketingplanitem = $marketingplanitem;
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
