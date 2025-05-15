<?php

namespace Workdo\FixEquipment\Events;

use Illuminate\Queue\SerializesModels;

class CreateAudit
{
    use SerializesModels;

    public $request;

    public $audit;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($request, $audit)
    {
        $this->request = $request;
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
