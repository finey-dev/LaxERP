<?php

namespace Workdo\FixEquipment\Events;

use Illuminate\Queue\SerializesModels;

class UpdateStatus
{
    use SerializesModels;

    public $request;

    public $status;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($request, $status)
    {
        $this->request = $request;
        $this->status = $status;
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
