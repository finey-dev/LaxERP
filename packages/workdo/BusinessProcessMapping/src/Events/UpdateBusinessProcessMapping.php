<?php

namespace Workdo\BusinessProcessMapping\Events;

use Illuminate\Queue\SerializesModels;

class UpdateBusinessProcessMapping
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $businessProcess;

    public function __construct($request, $businessProcess)
    {
        $this->request = $request;
        $this->businessProcess = $businessProcess;
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
