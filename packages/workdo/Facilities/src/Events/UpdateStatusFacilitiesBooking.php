<?php

namespace Workdo\Facilities\Events;

use Illuminate\Queue\SerializesModels;

class UpdateStatusFacilitiesBooking
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $facilitiesBooking;

    public function __construct($request , $facilitiesBooking)
    {
        $this->request = $request;
        $this->facilitiesBooking = $facilitiesBooking;
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
