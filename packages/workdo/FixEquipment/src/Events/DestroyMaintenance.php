<?php

namespace Workdo\FixEquipment\Events;

use Illuminate\Queue\SerializesModels;

class DestroyMaintenance
{
    use SerializesModels;

    public $maintenance;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($maintenance)
    {
        $this->$maintenance = $maintenance;
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
