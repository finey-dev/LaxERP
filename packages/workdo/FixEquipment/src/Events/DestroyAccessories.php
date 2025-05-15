<?php

namespace Workdo\FixEquipment\Events;

use Illuminate\Queue\SerializesModels;

class DestroyAccessories
{
    use SerializesModels;


    public $accessories;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($accessories)
    {
        $this->accessories = $accessories;
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
