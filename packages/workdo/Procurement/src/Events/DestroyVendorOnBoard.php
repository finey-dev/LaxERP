<?php

namespace Workdo\Procurement\Events;

use Illuminate\Queue\SerializesModels;

class DestroyVendorOnBoard
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $vendorBoard;

    public function __construct($vendorBoard)
    {
        $this->rfx = $vendorBoard;
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
