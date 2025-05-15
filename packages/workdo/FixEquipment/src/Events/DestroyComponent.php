<?php

namespace Workdo\FixEquipment\Events;

use Illuminate\Queue\SerializesModels;

class DestroyComponent
{
    use SerializesModels;

    public $component;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($component)
    {
        $this->component = $component;
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
