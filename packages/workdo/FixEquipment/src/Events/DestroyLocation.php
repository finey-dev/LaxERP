<?php

namespace Workdo\FixEquipment\Events;

use Illuminate\Queue\SerializesModels;

class DestroyLocation
{
    use SerializesModels;

    public $location;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($location)
    {
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
