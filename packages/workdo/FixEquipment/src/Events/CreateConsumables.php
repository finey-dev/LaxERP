<?php

namespace Workdo\FixEquipment\Events;

use Illuminate\Queue\SerializesModels;

class CreateConsumables
{
    use SerializesModels;

    public $request;

    public $consumables;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($request, $consumables)
    {
        $this->request = $request;
        $this->consumables = $consumables;
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
