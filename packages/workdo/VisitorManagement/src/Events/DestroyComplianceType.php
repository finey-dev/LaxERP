<?php

namespace Workdo\VisitorManagement\Events;

use Illuminate\Queue\SerializesModels;

class DestroyComplianceType
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $compliance_type;
    public function __construct($compliance_type)
    {
        $this->compliance_type = $compliance_type;
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
