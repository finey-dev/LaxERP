<?php

namespace Workdo\Facilities\Events;

use Illuminate\Queue\SerializesModels;

class UpdateFacilitiesHour
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $facilitiesWorking;

    public function __construct($request , $facilitiesWorking)
    {
        $this->request = $request;
        $this->facilitiesWorking = $facilitiesWorking;
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
