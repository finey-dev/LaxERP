<?php

namespace Workdo\Planning\Events;

use Illuminate\Queue\SerializesModels;

class UpdateCharter
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $Charter;

    public function __construct($request, $Charter)
    {
        $this->request = $request;
        $this->Charter = $Charter;
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
