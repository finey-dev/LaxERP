<?php

namespace Workdo\FixEquipment\Events;

use Illuminate\Queue\SerializesModels;

class UpdateMaintenance
{
    use SerializesModels;

    public $request;

    public $maintenance;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($request, $maintenance)
    {
        $this->request = $request;
        $this->maintenance = $maintenance;
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
