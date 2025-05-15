<?php

namespace Workdo\Requests\Events;

use Illuminate\Queue\SerializesModels;

class CreateRequestResponse
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $RequestResponse;

    public function __construct($request,$RequestResponse)
    {
        $this->request = $request;
        $this->RequestResponse = $RequestResponse;
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
