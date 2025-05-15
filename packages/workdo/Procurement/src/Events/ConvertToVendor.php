<?php

namespace Workdo\Procurement\Events;

use Illuminate\Queue\SerializesModels;

class ConvertToVendor
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $vendor;

    public function __construct($request, $vendor)
    {
        $this->request = $request;
        $this->vendor = $vendor;
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
