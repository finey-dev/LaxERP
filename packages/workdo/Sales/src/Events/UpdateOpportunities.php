<?php

namespace Workdo\Sales\Events;

use Illuminate\Queue\SerializesModels;

class UpdateOpportunities
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $opportunities;
    public function __construct($request, $opportunities)
    {
        $this->request = $request;
        $this->opportunities = $opportunities;
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
