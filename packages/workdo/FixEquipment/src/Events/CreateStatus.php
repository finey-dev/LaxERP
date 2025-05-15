<?php

namespace Workdo\FixEquipment\Events;

use Illuminate\Queue\SerializesModels;

class CreateStatus
{
    use SerializesModels;

    public $request;

    public $status;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($status, $request)
    {
        $this->status = $status;
        $this->request = $request;
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
