<?php

namespace Workdo\Facilities\Events;

use Illuminate\Queue\SerializesModels;

class UpdateFacilitiesSpace
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $facilitiesSpace;

    public function __construct($request , $facilitiesSpace)
    {
        $this->request = $request;
        $this->facilitiesSpace = $facilitiesSpace;
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
