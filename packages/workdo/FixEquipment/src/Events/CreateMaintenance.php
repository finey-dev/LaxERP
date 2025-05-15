<?php

namespace Workdo\FixEquipment\Events;

use Illuminate\Queue\SerializesModels;

class CreateMaintenance
{
    use SerializesModels;

    public $request;

    public $maintenance;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($request, $maintenaces)
    {
        $this->request = $request;
        $this->maintenance = $maintenaces;
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
