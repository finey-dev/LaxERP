<?php

namespace Workdo\FixEquipment\Events;

use Illuminate\Queue\SerializesModels;

class DestroyManufacturer
{
    use SerializesModels;

    public $manufacturer;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($manufacturer)
    {
        $this->manufacturer = $manufacturer;
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
