<?php

namespace Workdo\FixEquipment\Events;

use Illuminate\Queue\SerializesModels;

class CreateAccessories
{
    use SerializesModels;

    public $request;

    public $accessories;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($request, $accessories)
    {
        $this->request = $request;
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
