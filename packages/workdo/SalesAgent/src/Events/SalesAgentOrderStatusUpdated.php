<?php

namespace Workdo\SalesAgent\Events;

use Illuminate\Queue\SerializesModels;

class SalesAgentOrderStatusUpdated
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $order;
    public function __construct($order)
    {
        $this->order = $order;
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
