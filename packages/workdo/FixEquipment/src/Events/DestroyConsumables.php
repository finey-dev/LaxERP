<?php

namespace Workdo\FixEquipment\Events;

use Illuminate\Queue\SerializesModels;

class DestroyConsumables
{
    use SerializesModels;

    public $consumables;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($consumables)
    {
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
