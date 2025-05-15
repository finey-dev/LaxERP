<?php

namespace Workdo\Requests\Events;

use Illuminate\Queue\SerializesModels;

class DestroyRequestResponse
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $Response;

    public function __construct($Response)
    {
        $this->Response = $Response;
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
