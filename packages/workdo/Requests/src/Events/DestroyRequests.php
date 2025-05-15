<?php

namespace Workdo\Requests\Events;

use Illuminate\Queue\SerializesModels;

class DestroyRequests
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $Requests;

    public function __construct($Requests)
    {
        $this->Requests = $Requests;
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
