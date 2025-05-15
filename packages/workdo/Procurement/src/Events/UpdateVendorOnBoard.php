<?php

namespace Workdo\Procurement\Events;

use Illuminate\Queue\SerializesModels;

class UpdateVendorOnBoard
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $vendorOnBoard;
    public function __construct($request, $vendorOnBoard)
    {
        $this->request = $request;
        $this->vendorOnBoard = $vendorOnBoard;
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
