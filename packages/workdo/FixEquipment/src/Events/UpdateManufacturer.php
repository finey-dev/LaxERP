<?php

namespace Workdo\FixEquipment\Events;

use Illuminate\Queue\SerializesModels;

class UpdateManufacturer
{
    use SerializesModels;

    public $request;

    public $manufacturer;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($request, $manufacturer)
    {
        $this->request = $request;
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
