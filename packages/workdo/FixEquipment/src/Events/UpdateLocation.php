<?php

namespace Workdo\FixEquipment\Events;

use Illuminate\Queue\SerializesModels;

class UpdateLocation
{
    use SerializesModels;

    public $request;

    public $location;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($request, $location)
    {
        $this->request = $request;
        $this->location = $location;
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
