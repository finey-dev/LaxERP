<?php

namespace Workdo\MarketingPlan\Events;

use Illuminate\Queue\SerializesModels;

class CreateMarketingPlanItem
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $marketingplanitem;

    public function __construct($request, $marketingplanitem)
    {
        $this->request = $request;
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
