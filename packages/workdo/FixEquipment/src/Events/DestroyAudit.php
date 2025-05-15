<?php

namespace Workdo\FixEquipment\Events;

use Illuminate\Queue\SerializesModels;

class DestroyAudit
{
    use SerializesModels;

    public $audit;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($audit)
    {
        $this->audit = $audit;
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
