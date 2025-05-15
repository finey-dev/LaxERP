<?php

namespace Workdo\Facilities\Events;

use Illuminate\Queue\SerializesModels;

class DestroyFacilitiesBooking
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $facilitiesBooking;

    public function __construct($facilitiesBooking)
    {
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
